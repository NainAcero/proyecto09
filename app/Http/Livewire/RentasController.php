<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ClienteVehiculo;
use App\Models\Cajon;
use App\Models\Renta;
use App\Models\Tarifa;
use App\Models\Tipo;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\Carbon;
use DB;

class RentasController extends Component
{
    use WithPagination;

    public  $selected_id, $search,$buscarCliente,$barcode, $obj, $clientes, $clienteSelected;
    public  $nombre,$telefono,$celular,$email,$placa,$tipo,$total,$totalCalculado,$tiempo,
        $direccion,$modelo,$marca,$color,$fecha_ini, $fecha_fin,$nota,$arrayTarifas,$tarifaSelected,
        $vehiculo_id;

    private $pagination = 5;
    public  $section = 1;

    public function mount() {
        $this->arrayTarifas = Tarifa::all();
        if($this->arrayTarifas->count() > 0) $this->tarifaSelected = $this->arrayTarifas[0]->id;
    }

    public function render(){
        $clientes = null;
        $cajones = Cajon::join('tipos as t', 't.id', 'cajones.tipo_id')
            ->select('cajones.*', 't.descripcion as tipo','t.id as tipo_id', 't.imagen',
                DB::RAW("'' as tarifa_id "),
                DB::RAW("'' as barcode "),
                DB::RAW("0 as folio "),
                DB::RAW("'' as descripcion_coche ")
            )->get();

        if(strlen($this->buscarCliente) > 0) {
            $clientes = ClienteVehiculo::leftjoin('users as u', 'u.id','cliente_vehiculos.user_id')
                ->leftjoin('vehiculos as v', 'v.id', 'cliente_vehiculos.vehiculo_id')
                ->select('v.id as vehiculo_id','v.placa','v.marca','v.color','v.nota','v.modelo','u.id as cliente_id','nombre','telefono','movil','email','direccion')
                ->where('nombre', 'like', '%'. $this->buscarCliente .'%')
                ->get();
        }else {
            $clientes = User::where('tipo','Cliente')
                ->select('id','nombre','telefono','movil','email','direccion',
                    DB::RAW("'' as vehiculos ")
                )->take(1)->get();
        }

        $this->clientes = $clientes;

        foreach ($cajones as $c) {
            $tarifa = Tarifa::where('tipo_id', $c->tipo_id)->select('id')->first();
            $c->tarifa_id = $tarifa['id'];

            $renta = Renta::where('cajon_id', $c->id)
                ->select('barcode','id','descripcion as descripcion_coche')
                ->where('status','ABIERTO')
                ->orderBy('id','desc')
                ->first();

            $c->barcode = ($renta['barcode'] ?? '');
            //$c->barcode = ($renta['barcode'] == null ? '': $renta['barcode']);
            $c->folio = ($renta['id'] ?? '' );
            //$c->folio = ($renta['id'] == null ? '': $renta['id']);
            $c->descripcion_coche = ($renta['descripcion_coche'] ?? '');
            //$c->descripcion_coche = ($renta['descripcion_coche'] == null ? '': $renta['descripcion_coche']);

        }
        return view('livewire.rentas.component', [
            'cajones' => $cajones
        ]);
    }

    public function doCheckOut($barcode, $section = 2){
        $bcode = ($barcode == '' ? $this->barcode : $barcode);
        $obj = Renta::where('barcode',$bcode)->select('*', DB::RAW("'' as tiempo "), DB::RAW("0 as total "))->first();

        if($obj !=null ) {
            $this->section = $section;
            $this->barcode = $bcode;

            $start  =  Carbon::parse($obj->acceso);
            $end    = new \DateTime(Carbon::now());
            $obj->tiempo= $start->diffInHours($end) . ':' . $start->diff($end)->format('%I:%S');

            $obj->total = $this->calculateTotal($obj->acceso, $obj->tarifa_id);
            $this->obj = $obj;
        }else{
            $this->emit('msgok', 'No existe el código de barras');
            $this->barcode ='';
            return;
        }
    }

    public function calculateTotal($fromDate, $tarifaId, $toDate = ''){
        $fraccion = 0;
        $tarifa = Tarifa::where('id', $tarifaId)->first();
        $start  =  Carbon::parse($fromDate);
        $end    =  new \DateTime(Carbon::now());
        if(!$toDate =='')   $end = Carbon::parse($toDate);

        $tiempo= $start->diffInHours($end) . ':' . $start->diff($end)->format('%I:%S');//dif en horas + dif en min y seg

        $minutos = $start->diffInMinutes($end);
        $horasCompletas = $start->diffInHours($end);

        if($minutos <= 65){
            $fraccion = $tarifa->costo;
        }else{
            $m = ($minutos % 60);
            if ( in_array($m, range(0,5)) ) { // después de la 1ra hora, se dan 5 minutos de tolerancia al cliente

            }else if ( in_array($m, range(6,30)) ){
                $fraccion = ($tarifa->costo / 2);   //después de la 1ra hora, del minuto 6 al 30 se cobra 50% de la tarifa ($6.50)
            }else if ( in_array($m, range(31,59)) ){
                $fraccion = $tarifa->costo;    //después de la 1ra hora, del minuto 31-60 se cobra tarifa completa ($13.00)
            }
        }
        //retornamos el total a cobrar
        $total = (($horasCompletas * $tarifa->costo) + $fraccion);
        return $total;
    }

    //este método registra la entrada de vehículos
    public function RegistrarEntrada($tarifa_id, $cajon_id, $estatus = '', $comment =''){
        if($estatus == 'OCUPADO') {
            $this->emit('msgok','El cajón ya está ocupado');
            return;
        }

        $cajon = Cajon::where('id', $cajon_id)->first();
        $cajon->status = 'OCUPADO';
        $cajon->save();

        $renta = Renta::create([
            'acceso' => Carbon::now(),
            'user_id' => auth()->user()->id,
            'tarifa_id' => $tarifa_id,
            'cajon_id' => $cajon_id,
            'descripcion' =>$comment
        ]);

        $renta->barcode = sprintf('%07d', $renta->id);
        $renta->save();

        $this->barcode ='';
        $this->descripcion ='';
        $this->emit('getin-ok','Entrada Registrada en Sistema');
        $this->emit('print', $renta->id);
    }


    //Ticket rápido de entrada de vehículos
    public function TicketVisita(){
        $tarifas = Tarifa::select('jerarquia','tipo_id','id')->orderBy('jerarquia','desc')->get();
        $tarifaID = null;

        foreach ($tarifas as $j) {
            $cajon = Cajon::where('status','DISPONIBLE')->where('tipo_id',$j->tipo_id)->first();
            if($cajon) {
                $tarifaID = $j->id;
                break;
            }
        }

        if($cajon == null){
            $this->emit('msg-ops','Todos los espacios/cajones del estacionamiento están ocupados');
            return;
        }

        $cajon->status = 'OCUPADO';
        $cajon->save();

        $renta = Renta::create([
            'acceso' => Carbon::now(),
            'user_id' => auth()->user()->id,
            'tarifa_id' => $tarifaID,
            'cajon_id' => $cajon->id
        ]);

        $renta->barcode = sprintf('%07d', $renta->id);
        $renta->save();

        $this->barcode ='';
        $this->emit('getin-ok','Entrada Registrada en Sistema');
    }

    public function RegistrarTicketRenta(){
        $rules = [
            'nombre'     => 'required|min:3',
            'direccion'    => 'required',
            'placa'    => 'required',
            'email' =>'nullable|email'
        ];

        $customMessages = [
            'nombre.required' => 'El campo Nombre es obligatorio',
            'direccion.required' => 'Por favor ingresa la Dirección',
            'placa.required' => 'Debes ingresar el número de Placa'
        ];

        $this->validate($rules, $customMessages);

        $exist = Renta::where('placa',$this->placa)->where('vehiculo_id', '>', 0)->where('status','ABIERTO')->count();
        if($exist > 0) {
            $this->emit('msg-error',"La placa $this->placa tiene una renta registrada aún con vigencia");
            return;
        }

        DB::beginTransaction();

        try {

            if($this->clienteSelected > 0) {
                $cliente = User::find($this->clienteSelected);
            }else{
                if(empty($this->email)) $this->email = str_replace(' ','_', $this->nombre) .'_'. uniqid() . '_@sysparking.com';
                $cliente = User::create([
                    'nombre' => $this->nombre,
                    'telefono' => $this->telefono,
                    'movil' => $this->celular,
                    'direccion' => $this->direccion,
                    'tipo' => 'Cliente',
                    'email' =>  $this->email,
                    'password' => bcrypt('secret2020.')
                ]);
            }

            if($this->clienteSelected == null ) {
                $vehiculo = Vehiculo::create([
                    'placa' =>$this->placa,
                    'modelo' =>$this->modelo,
                    'marca' =>$this->marca,
                    'color' =>$this->color,
                    'nota' =>$this->nota
                ]);
            }

            //paso 3 registrar la asociacion de vehiculos y clientes
            if($this->clienteSelected == null ) {
                $cv = ClienteVehiculo::create([
                    'user_id' => $cliente->id,
                    'vehiculo_id' => $vehiculo->id
                ]);
            }

            //paso 4 registrar el ticket en rentas
            $renta = Renta::create([
                'acceso' => Carbon::parse($this->fecha_ini),
                'salida' => Carbon::parse($this->fecha_fin),
                'user_id' => auth()->user()->id,
                'tarifa_id' => $this->tarifaSelected,
                'placa' =>$this->placa,
                'modelo' =>$this->modelo,
                'marca' =>$this->marca,
                'color' =>$this->color,
                'descripcion' =>$this->nota,
                'direccion' =>$this->direccion,
                'vehiculo_id' => ($this->clienteSelected == null ? $vehiculo->id : $this->vehiculo_id), //AGREGAR
                'total' =>$this->total,
                'hours' =>$this->tiempo
            ]);

            $renta->barcode = sprintf('%07d', $renta->id);
            $renta->save();

            //enviamos feedback
            $this->barcode ='';
            $this->emit('getin-ok','Se registró el cliente por Renta');
            $this->emit('print-pension', $renta->id);
            $this->action = 1;
            $this->section = 1;
            $this->limpiarCliente();

            //confirmamos transacción
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $status = $e->getMessage();
            dd($e);
        }
    }

    public function BuscarTicket(){
        $nuevoTotal = 0;

        $rules = [
            'barcode'     => 'required'
        ];

        $customMessages = [
            'barcode.required' => 'Ingresa o Escanea el Código de Barras'
        ];

        $this->validate($rules, $customMessages);

        $ticket = Renta::where('barcode', $this->barcode)->select('*')->first();
        if($ticket){
            if($ticket->status == 'CERRADO') {
                $this->emit('msg-ops', 'El ticket ya tiene registrada la salida-');
                $this->barcode ='';
                return;
            }
        }else{
            $this->emit('msg-ops', 'El código de ticket no existe en sistema');
            $this->barcode ='';
            return;
        }

        // $tarifa = Tarifa::where('id', $ticket->tarifa_id)->first();
        $tiempo = $this->CalcularTiempo($ticket->acceso);
        $nuevoTotal =  $this->calculateTotal($ticket->acceso, $ticket->tarifa_id);

        $ticket->salida = Carbon::now();
        $ticket->status = 'CERRADO';
        if($ticket->vehiculo_id == null)  $ticket->total = $nuevoTotal;
        $ticket->hours = $tiempo;
        $ticket->save();

        if($ticket->cajon_id > 0 ){
            $cajon = Cajon::where('id', $ticket->cajon_id)->first();
            $cajon->status = 'DISPONIBLE';
            $cajon->save();
        }

        if($ticket){
            $this->barcode ='';
            $this->section = 1;
            $this->emit('getout-ok', 'Salida Registrada Con Éxito');
        } else {
           $this->barcode ='';
           $this->barcode ='';
           $this->emit('getout-error', 'No se pudo registrar sa salida :/');
        }
    }

    public function CalcularTiempo($fechaEntrada) {
       $start  = Carbon::parse($fechaEntrada);
       $end    = new \DateTime(Carbon::now());
       $tiempo = $start->diffInHours($end) . ':' . $start->diff($end)->format('%I:%S');
       return $tiempo;
    }

    public function getSalida() {

        if($this->tiempo <=0) {
            $this->total = number_format(0,2);
            $this->fecha_fin = '';
        }
        else {
            $this->fecha_fin = Carbon::now()->addMonths($this->tiempo)->format('d-m-Y');
            $tarifa = Tarifa::where('tiempo','Mes')->select('costo')->first();

            if($tarifa->count()) {
                $this->total = $this->tiempo * $tarifa->costo;
            }
        }
    }

    public function mostrarCliente($cliente){

        $this->clientes = '';
        $this->buscarCliente ='';
        $clienteJson = json_decode($cliente);

        $this->nombre = $clienteJson->nombre;
        $this->telefono = $clienteJson->telefono;
        $this->celular = $clienteJson->movil;
        $this->email = $clienteJson->email;
        $this->direccion = $clienteJson->direccion;

        $this->placa = $clienteJson->placa;
        $this->modelo = $clienteJson->modelo;
        $this->color = $clienteJson->color;
        $this->marca = $clienteJson->marca;
        $this->nota = $clienteJson->nota;
        $this->vehiculo_id = $clienteJson->vehiculo_id;
        $this->clienteSelected = $clienteJson->cliente_id;
    }

    public function limpiarCliente() {
        $this->nombre = '';
        $this->telefono = '';
        $this->celular = '';
        $this->email = '';
        $this->direccion = '';

        $this->placa = '';
        $this->modelo = '';
        $this->color = '';
        $this->marca = '';
        $this->nota = '';
        $this->clienteSelected = null;
        $this->vehiculo_id = null; //AGREGAR
    }

    protected $listeners = [
        'RegistrarEntrada'   => 'RegistrarEntrada',
        'doCheckOut'       => 'doCheckOut',
        'doCheckIn' => 'RegistrarEntrada'
    ];
}

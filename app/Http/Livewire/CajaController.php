<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Caja;

class CajaController extends Component
{
    use WithPagination;

    public $tipo = 'Elegir', $concepto, $monto, $comprobante, $event;
    public $selected_id, $search;
    public $action = 1, $pagination = 5;

    public function mount() {
        $this->event = false;
    }

    public function render(){
        if(strlen($this->search) > 0){
            $cajas = Caja::leftjoin('users as u', 'u.id', 'cajas.user_id')
                ->select('cajas.*', 'u.nombre')
                ->where('tipo', 'like', '%'.$this->search.'%')
                ->orWhere('concepto', 'like', '%'.$this->search.'%')
                ->paginate($this->pagination);

            return view('livewire.movimientos.component', [
                "info" => $cajas
            ]);
        }else{
            $cajas = Caja::leftjoin('users as u', 'u.id', 'cajas.user_id')
                ->select('cajas.*', 'u.nombre')
                ->orderBy('id', 'desc')
                ->paginate($this->pagination);

            return view('livewire.movimientos.component', [
                "info" => $cajas
            ]);
        }
    }

    // Paginación
    public function updatingSearch(): void{
        $this->gotoPage(1);
    }

    public function doAction($action){
        $this->resetInput();
        $this->action = $action;
    }

    public function resetInput(){
        $this->concepto = '';
        $this->tipo = 'Elegir';
        $this->monto = '';
        $this->comprobante = '';
        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
        $this->event = false;
    }

    public function edit($id){
        $record = Caja::findOrFail($id);
        $this->selected_id = $id;
        $this->tipo = $record->tipo;
        $this->concepto = $record->concepto;
        $this->monto = $record->monto;
        $this->comprobante = $record->comprobante;
        $this->action = 2;
        $this->event = false;
    }

    public function StoreOrUpdate(){

        $this->validate([
            'tipo' => 'required',
            'tipo' => 'not_in:Elegir',
            'monto' => 'required',
            'concepto' => 'required'
        ]);

        if($this->selected_id <= 0){
            $caja = Caja::create([
                'monto' => $this->monto,
                'tipo' => $this->tipo,
                'concepto' => $this->concepto,
                'user_id' => Auth::user()->id
            ]);

            if($this->comprobante && $this->event){
                $image = $this->comprobante;
                $fileName = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                $moved = \Image::make($image)->save('images/movs/'.$fileName);

                if($moved){
                    $caja->comprobante = $fileName;
                    $caja->save();
                }
            }

            $this->emit('msgok', 'Movimiento en Caja Creada con Éxito');
        }else{
            $record = Caja::find($this->selected_id);
            $record->update([
                'monto' => $this->monto,
                'tipo' => $this->tipo,
                'concepto' => $this->concepto,
                'user_id' => Auth::user()->id
            ]);

            if($this->comprobante && $this->event ){
                $image = $this->comprobante;
                $fileName = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                $moved = \Image::make($image)->save('images/movs/'.$fileName);

                if($moved){
                    $record->comprobante = $fileName;
                    $record->save();
                }
            }

            $this->emit('msgok', 'Movimiento en Caja Actiualizada con Éxito');
        }

        $this->resetInput();
    }

    protected $listeners = [
        'deleteRow'     => 'destroy',
        'fileUpload' =>  'handleFileUpload'
    ];

    public function handleFileUpload($imageData){
        $this->comprobante = $imageData;
        $this->event = true;
    }

    public function destroy(int $id){
        try {
            $record = Caja::findOrFail($id);
            $record->delete();
            $this->resetInput();
            $this->emit('msgok', 'Registro eliminado con éxito');
        } catch (\Exception $exception) {
            dd($exception);
        }
    }
}

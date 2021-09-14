<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tarifa;
use App\Models\Tipo;
use DB;

class TarifasController extends Component
{

    use WithPagination;

    public $tiempo = 'Elegir', $tipo = 'Elegir', $descripcion, $costo, $jerarquia;
    public $selected_id, $search;
    public $action = 1;
    public $pagination = 5;
    public $tipos;

    public function mount(){
        $this->getJerarquia();
    }

    public function getJerarquia(){
        $tarifa = Tarifa::count();
        if($tarifa > 0){
            $tarifa = Tarifa::select('jerarquia')->orderBy('jerarquia', 'DESC')->first();
            $this->jerarquia = $tarifa->jerarquia + 1;
        }else{
            $this->jerarquia = 0;
        }
    }

    public function render(){
        $this->tipos = Tipo::all();
        if(strlen($this->search) > 0){
            $info = Tarifa::leftjoin('tipos as t', 't.id', 'tarifas.tipo_id')
                ->where('tarifas.descripcion', 'like', '%'.$this->search.'%')
                ->orWhere('tarifas.tiempo', 'like', '%'.$this->search.'%')
                ->select('tarifas.*', 't.descripcion as tipo')
                ->orderBy('tarifas.tiempo', 'desc')
                ->orderBy('t.descripcion')
                ->paginate($this->pagination);
            return view('livewire.tarifas.component', [
                'info' => $info
            ]);
        }else{
            $info = Tarifa::leftjoin('tipos as t', 't.id', 'tarifas.tipo_id')
                ->select('tarifas.*', 't.descripcion as tipo')
                ->orderBy('tarifas.tiempo', 'desc')
                ->orderBy('t.descripcion')
                ->paginate($this->pagination);
            return view('livewire.tarifas.component', [
                'info' => $info
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
        $this->descripcion = '';
        $this->tiempo = '';
        $this->costo = '';
        $this->tipo = 'Elegir';
        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
    }

    public function edit($id){
        $record = Tarifa::findOrFail($id);
        $this->selected_id = $id;
        $this->descripcion = $record->descripcion;
        $this->tiempo = $record->tiempo;
        $this->costo = $record->costo;
        $this->tipo = $record->tipo->id;
        $this->descripcion = $record->descripcion;
        $this->jerarquia = $record->jerarquia;
        $this->action = 2;
    }

    public function StoreOrUpdate(){
        $this->validate([
            'tiempo' => 'required',
            'costo'  => 'required',
            'tipo'   => 'required',
            'tiempo' => 'not_in:Elegir',
            'tipo'   => 'not_in:Elegir'
        ]);

        if($this->selected_id > 0) {
            $existe = Tarifa::where('tiempo', $this->tiempo)
            ->where('tipo_id', $this->tipo)
            ->where('id', '<>', $this->selected_id)
            ->select('tiempo')->get();
        }else {
            $existe = Tarifa::where('tiempo', $this->tiempo)
            ->where('tipo_id', $this->tipo)
            ->select('tiempo')->get();
        }

        if( $existe->count() > 0){
            $this->emit('msg-error', 'Ya existe la tarifa');
            $this->resetInput();
            return;
        }

        if($this->selected_id <= 0) {
            $tarifa =  Tarifa::create([
                'tiempo' => $this->tiempo,
                'descripcion' => $this->descripcion,
                'costo' => $this->costo,
                'tipo_id' => $this->tipo,
                'jerarquia' => $this->jerarquia
            ]);
            $this->emit('msgok', 'Tarifa Creada');
        }else {
            $tarifa = Tarifa::find($this->selected_id);
            $tarifa->update([
                'tiempo' => $this->tiempo,
                'descripcion' => $this->descripcion,
                'costo' => $this->costo,
                'tipo_id' => $this->tipo,
                'jerarquia' => $this->jerarquia
            ]);
            $this->emit('msgok', 'Tarifa Actualizada');
        }

        if($this->jerarquia == 1){
            // Tarifa::where('id','<>',$tarifa->id)->update([
            //     'jerarquia' => 0
            // ]);
        }

        $this->resetInput();
        $this->getJerarquia();
    }

    public function createFromModal($info){
        $data = json_decode($info);
        $this->selected_id = $data->id;
        $this->tiempo = $data->tiempo;
        $this->tipo = $data->tipo;
        $this->costo = $data->costo;
        $this->descripcion = $data->descripcion;
        $this->jerarquia = $data->jerarquia;

        $this->StoreOrUpdate();
    }

    public function destroy(int $id){
        try {
            $record = Tarifa::findOrFail($id);
            $record->delete();
            $this->resetInput();
            $this->emit('msgok', 'Registro eliminado con éxito');
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    protected $listeners = [
        'deleteRow'         => 'destroy',
        'createFromModal'   => 'createFromModal'
    ];

}

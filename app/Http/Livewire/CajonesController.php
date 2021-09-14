<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cajon;
use App\Models\Tipo;

class CajonesController extends Component
{
    use WithPagination;

    public $tipo = 'Elegir', $descripcion , $status = 'DISPONIBLE', $tipos;
    public $selected_id, $search;
    public $action = 1, $pagination = 5;

    public function render(){
        $this->tipos = Tipo::all();

        if(strlen($this->search) > 0){
            $info = Cajon::leftjoin('tipos as t', 't.id', 'cajones.tipo_id')
                ->select('cajones.*', 't.descripcion as tipo')
                ->where('cajones.descripcion', 'like', '%'.$this->search.'%')
                ->orWhere('cajones.status', 'like', '%'.$this->search.'%')
                ->paginate($this->pagination);

            return view('livewire.cajones.component', [
                "info" => $info
            ]);
        } else{
            $info = Cajon::leftjoin('tipos as t', 't.id', 'cajones.tipo_id')
                ->select('cajones.*', 't.descripcion as tipo')
                ->orderBy('cajones.id', 'desc')
                ->paginate($this->pagination);

            return view('livewire.cajones.component', [
                "info" => $info
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
        $this->tipo = 'Elegir';
        $this->status = 'DISPONIBLE';
        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
    }

    public function edit($id){
        $record = Cajon::findOrFail($id);
        $this->selected_id = $id;
        $this->tipo = $record->tipo_id;
        $this->descripcion = $record->descripcion;
        $this->status = $record->status;
        $this->action = 2;
    }

    public function StoreOrUpdate(){

        $this->validate([
            'tipo' => 'required',
            'tipo' => 'not_in:Elegir',
            'descripcion' => 'required',
            'status' => 'required'
        ]);

        if($this->selected_id <= 0){
            $cajon = Cajon::create([
                'descripcion' => $this->descripcion,
                'tipo_id' => $this->tipo,
                'status' => $this->status
            ]);
            $this->emit('msgok', 'Cajón fué creado con Éxito');
        }else{
            $record = Cajon::find($this->selected_id);
            $record->update([
                'descripcion' => $this->descripcion,
                'tipo_id' => $this->tipo,
                'status' => $this->status
            ]);
            $this->emit('msgok', 'Cajón Actualizado con Éxito');
        }

        $this->resetInput();
    }

    public function destroy(int $id){
        try {
            $record = Cajon::findOrFail($id);
            $record->delete();
            $this->resetInput();
            $this->emit('msgok', 'Registro eliminado con éxito');
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    protected $listeners = [
        'deleteRow' => 'destroy'
    ];
}

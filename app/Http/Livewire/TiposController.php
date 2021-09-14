<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Tipo;

class TiposController extends Component
{
    use WithPagination;

    public $descripcion, $image;
    public $selected_id, $search;
    public $action = 1, $event;

    private $pagination = 5;

    public function mount(){
        $this->event =false;
    }

    public function render(){
        if(strlen($this->search) > 0){
            $info = Tipo::where('descripcion', 'like', '%'.$this->search.'%')
                        ->paginate($this->pagination);
            return view('livewire.tipos.component', [
                'info' => $info
            ]);
        }else{
            $info = Tipo::paginate($this->pagination);
            return view('livewire.tipos.component', [
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
        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
        $this->event = false;
    }

    public function edit(int $id){
        $record = Tipo::findOrFail($id);

        $this->descripcion = $record->descripcion;
        $this->selected_id = $record->id;
        $this->action = 2;
    }

    public function StoreOrUpdate(){
        $this->validate([
            'descripcion' => 'required|min:4'
        ]);

        if($this->selected_id > 0)
            $existe = Tipo::where('descripcion', $this->descripcion)
                        ->where('id', '<>', $this->selected_id)->select('descripcion')->get();
        else
            $existe = Tipo::where('descripcion', $this->descripcion)
                        ->select('descripcion')->get();

        if($existe->count() > 0){
            session()->flash('msg-error', 'Ya existe otro registro con la misma descripción');
            $this->resetInput();
            return;
        }

        if($this->selected_id <= 0){
            $record = Tipo::create([
                'descripcion' => $this->descripcion
            ]);

            if($this->image  && $this->event) {
                $image = $this->image;
                $fileName = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                $moved = \Image::make($image)->save('images/'.$fileName);

                if($moved) {
                    $record ->imagen = $fileName;
                    $record->save();
                }
            }
            session()->flash('message', 'Tipo Creado');
        }else{
            $record = Tipo::findOrFail($this->selected_id);
            $record->update([
                'descripcion' => $this->descripcion
            ]);

            if($this->image  && $this->event) {
                $image = $this->image;
                $fileName = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                $moved = \Image::make($image)->save('images/'.$fileName);

                if($moved)  {
                    $record ->imagen = $fileName;
                    $record->save();
                }
            }
            session()->flash('message', 'Tipo Actualizado');
        }

        $this->resetInput();
    }

    public function destroy(int $id){
        try {
            $record = Tipo::findOrFail($id);
            $record->delete();
            $this->resetInput();
            session()->flash('message', 'Tipo eliminado con Éxito');
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    public function handleFileUpload($imageData) {
        $this->image = $imageData;
        $this->event = true;
    }

    protected $listeners = [
        'deleteRow' => 'destroy',
        'fileUpload' =>'handleFileUpload'
    ];
}

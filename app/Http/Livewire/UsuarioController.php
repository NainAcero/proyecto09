<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UsuarioController extends Component
{
    use WithPagination;

    public $tipo = "Elegir", $nombre, $telefono, $movil, $email, $direccion, $password;
    public $selected_id, $search;
    public $action = 1, $pagination = 5;

    public function render(){
        if(strlen($this->search) > 0){
            $info = User::where('nombre', 'like', '%'.$this->search.'%')
                ->orWhere('telefono', 'like', '%'.$this->search.'%')
                ->paginate($this->pagination);

            return view('livewire.usuario.component', [
                'info' => $info
            ]);
        }else{
            $info = User::orderBy('id','desc')
    		    ->paginate($this->pagination);

            return view('livewire.usuario.component', [
                'info' => $info
            ]);
        }
    }

    public function updatingSearch(){
    	$this->gotoPage(1);
    }

    public function doAction($action){
    	$this->resetInput();
    	$this->action = $action;

    }

    private function resetInput(){
    	$this->nombre = '';
    	$this->tipo = 'Elegir';
    	$this->telefono = '';
    	$this->email = '';
    	$this->movil = '';
    	$this->direccion = '';
    	$this->password = '';
    	$this->selected_id = null;
    	$this->action = 1;
    	$this->search = '';
    }

    public function edit($id){
    	$record = User::findOrFail($id);
    	$this->selected_id = $id;
    	$this->nombre = $record->nombre;
    	$this->telefono = $record->telefono;
    	$this->movil = $record->movil;
    	$this->email = $record->email;
    	$this->direccion = $record->direccion;
    	$this->tipo = $record->tipo;
    	$this->action = 2;
    }

    public function StoreOrUpdate(){
    	$this->validate([
    		'nombre' => 'required',
    		'password'  => 'required',
    		'email'   => 'required|email',
    		'tipo'   => 'required|not_in:Elegir'
    	]);

    	if($this->selected_id <= 0) {
    		$user =  User::create([
    			'nombre' => $this->nombre,
    			'telefono' => $this->telefono,
    			'movil' => $this->movil,
    			'tipo' => $this->tipo,
    			'email' => $this->email,
    			'direccion' => $this->direccion,
    			'password' => bcrypt($this->password)
    		]);
            $this->emit('msgok', 'Usuario Creado');
    	}
    	else
    	{
    		$user = User::find($this->selected_id);
    		$user->update([
    			'nombre' => $this->nombre,
    			'telefono' => $this->telefono,
    			'movil' => $this->movil,
    			'tipo' => $this->tipo,
    			'email' => $this->email,
    			'direccion' => $this->direccion,
    			'password' => bcrypt($this->password)
    		]);
            $this->emit('msgok', 'Usuario Actualizado');
    	}

    	$this->resetInput();
    }

    public function destroy($id){
        try {
            if($id == auth()->user()->id) {
                $this->emit('msg-error', "Este usuario tiene una sesión abierta, no es posible eliminarlo");
                return;
            }

            $user = User::where('id', $id);
    		$user->delete();
    		$this->resetInput();
            $this->emit('msgok', "Usuario eliminado de sistema");
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    protected $listeners = [
    	'deleteRow'     => 'destroy'
    ];
}






















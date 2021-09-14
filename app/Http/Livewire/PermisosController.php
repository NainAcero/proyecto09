<?php

namespace App\Http\Livewire;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\Component;
use App\Models\User;
use DB;

class PermisosController extends Component
{
    public $permisoTitle ='Crear',$roleTitle ='Crear', $userSelected;
	public $tab = 'roles', $roleSelected;

    public function render(){
        $roles = Role::select('*', DB::RAW("0 as checked "))->get();
		$permisos = Permission::select('*', DB::RAW("0 as checked "))->get();

        if($this->userSelected !='' && $this->userSelected!='Seleccionar') {
			foreach ($roles as $r) {
				$user = User::find($this->userSelected);
				$tieneRole = $user->hasRole($r->name);
				if($tieneRole) {
					$r->checked = 1;
				}
			}
		}

        if($this->roleSelected !='' && $this->roleSelected!='Seleccionar' ) {
			foreach ($permisos as $p) {
				$role = Role::find($this->roleSelected);
				$tienePermiso = $role->hasPermissionTo($p->name);
				if($tienePermiso) {
					$p->checked = 1;
				}
			}
		}

        return view('livewire.permisos.component', [
            'roles'     =>  $roles,
			'permisos'  =>  $permisos, //*
			'usuarios'  => User::select('id','nombre')->get()
        ]);
    }

    /*-----------------------------------------------------------------------
							SECCION ROLES
	-----------------------------------------------------------------------
	*/

    public function resetInput() {
	  	$this->roleTitle ='Crear';
	  	$this->permisoTitle ='Crear';
	  	$this->userSelected ='';  //*
	  	$this->roleSelected ='';  //*
	}

    public function CrearRole($roleName, $roleId) {
	  	if($roleId)
	  		$this->UpdateRole($roleName, $roleId);
	  	else
	  		$this->SaveRole($roleName);
	}

    public function SaveRole($roleName) {
	  	$role = Role::where('name', $roleName)->first();

	  	if ($role) {
	  		$this->emit('msg-error', 'El role que intentas registrar ya existe en sistema');
	  		return;
	  	}

	  	Role::create([
	  		'name' => $roleName
	  	]);

	  	$this->emit('msg-ok', 'Se registró el role correctamente');
	  	$this->resetInput();

	}

    public function UpdateRole($roleName, $roleId) {
	  	$role = Role::where('name', $roleName)->where('id', '<>', $roleId)->first();      //*

	  	if ($role) {	                                                                   //*
	  		$this->emit('msg-error', 'El role que intentas registrar ya existe en sistema');
	  		return;
	  	}	                                                                               //*

	  	$role = Role::findOrFail($roleId);
	  	$role->name = $roleName;
	  	$role->save();
	  	$this->emit('msg-ok', 'Se actualizó el role correctamente');
	  	$this->resetInput();
	}

    public function AsignarRoles($rolesList) {
        if($this->userSelected > 0 ){
            $user = User::find($this->userSelected);

            if($user) {
                $user->syncRoles($rolesList);
                $this->emit('msg-ok', 'Roles asignados correctamente');
                $this->resetInput();
            }
        }
    }

    public function destroyRole(int $roleId) {
        try {
            Role::findOrFail($roleId)->delete();
            $this->emit('msg-ok', 'Se eliminó el role correctamente');
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    /*-----------------------------------------------------------------------
  							SECCION PERMISOS  - AQUI
	  -----------------------------------------------------------------------
	*/

    public function CrearPermiso($permisoName, $permisoId) {
	  	if($permisoId)
	  		$this->UpdatePermiso($permisoName, $permisoId);
	  	else
	  		$this->SavePermiso($permisoName);
	}

    public function SavePermiso($permisoName) {
	  	$permiso = Permission::where('name', $permisoName)->first();

	  	if ($permiso) {
	  		$this->emit('msg-error', 'El permiso que intentas registrar ya existe en sistema');
	  		return;
	  	}

	  	Permission::create(['name' => $permisoName]);
	  	$this->emit('msg-ok', 'Permiso Registrado correctamente');
	  	$this->resetInput();
	}

    public function UpdatePermiso($permisoName, $permisoId) {
	  	$permiso = Permission::where('name', $permisoName)->where('id','<>',$permisoId)->first();

	  	if ($permiso) {
	  		$this->emit('msg-error', 'El permiso que intentas registrar ya existe en sistema');
	  		return;
	  	}

	  	$permiso = Permission::findOrFail($permisoId)	;
	  	$permiso->name = $permisoName;
	  	$permiso->save();
	  	$this->emit('msg-ok', 'Permiso Actualizado correctamente');
	  	$this->resetInput();
	}

    public function AsignarPermisos($permisosList, $roleId) {
        if($roleId > 0 ){
            $role = Role::find($roleId);

            if($role) {
                $role->syncPermissions($permisosList);
                $this->emit('msg-ok', 'Permisos asignados correctamente');
                $this->resetInput();
            }
        }
    }

    public function destroyPermiso($permisoId){
        try {
            Permission::findOrFail($permisoId)->delete();
            $this->emit('msg-ok', 'Permiso Eliminado correctamente');
        } catch (\Exception $exception) {
            dd($exception);
        }
	}

    protected $listeners =[
        'destroyRole'     => 'destroyRole',
        'destroyPermiso'  => 'destroyPermiso',
        'CrearPermiso'    => 'CrearPermiso',
        'CrearRole'       => 'CrearRole',
        'AsignarRoles'    => 'AsignarRoles',
        'AsignarPermisos'    => 'AsignarPermisos'
    ];
}

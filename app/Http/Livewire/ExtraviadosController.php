<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Renta;
use DB;
use App\Traits\GenericTrait;

class ExtraviadosController extends Component
{
    use WithPagination;
	use GenericTrait;

	public $search;
    private $pagination = 10;

    public function render() {
        if(strlen($this->search) > 0) {
			$rentas = Renta::leftjoin('tarifas as t', 't.id', 'rentas.tarifa_id')
			    ->leftjoin('users as u', 'u.id', 'rentas.user_id')
			    ->select('rentas.*', 't.costo as tarifa','t.descripcion as vehiculo', 'u.nombre as usuario',DB::RAW("0 as pago"))
			    ->where('status','ABIERTO')->where('rentas.descripcion', 'like', "%". $this->search ."%")->where('vehiculo_id',null)
			    ->orderBy('id','desc')
			    ->paginate($this->pagination);
		}else {
			$rentas = Renta::leftjoin('tarifas as t', 't.id', 'rentas.tarifa_id')
			    ->leftjoin('users as u', 'u.id', 'rentas.user_id')
			    ->select('rentas.*', 't.costo as tarifa','t.descripcion as vehiculo', 'u.nombre as usuario',DB::RAW("0 as pago"))
			    ->where('status','ABIERTO')->where('vehiculo_id',null)
			    ->orderBy('id','desc')
			    ->paginate(10);
		}

        foreach ($rentas as $r) {
			$total= $this->DameTotal($r->acceso, $r->tarifa_id);
			$r->pago = number_format($total,2);
		}

        return view('livewire.extraviados.component-extraviados', [
            'info' => $rentas
        ]);
    }

    public function updatingSearch() {
		$this->gotoPage(1);
	}

    public function SalidaVehiculo($barcode) {
		$this->Salidas($barcode, 'multa por ticket extraviado $50.00');
	}

    protected $listeners = [
		'doCheckOut' => 'SalidaVehiculo'
	];
}

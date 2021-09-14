<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Renta;
use Carbon\Carbon;

class VentasDiariasController extends Component
{
    use WithPagination;

	public $fecha_ini, $fecha_fin;
    private $pagination = 10;

    public function render() {
        $ventas = Renta::leftjoin('tarifas as t', 't.id', 'rentas.tarifa_id')
            ->leftjoin('users as u', 'u.id', 'rentas.user_id')
            ->select('rentas.*', 't.costo as tarifa','t.descripcion as vehiculo', 'u.nombre as usuario')
            ->whereDate('rentas.created_at', Carbon::today())
            ->orderBy('id','desc')
            ->paginate($this->pagination);

        $total = Renta::whereDate('rentas.created_at', Carbon::today())->where('status','CERRADO')->sum('total');

        return view('livewire.reportes.component-ventas-diarias', [
            'info' => $ventas,
            'sumaTotal' => $total
        ]);
    }

    public function updatingSearch(){
    	$this->gotoPage(1);
    }
}

<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Renta;
use Carbon\Carbon;

class ReportePorFechasController extends Component
{
    use WithPagination;
	public $fecha_ini, $fecha_fin;
    private $pagination = 10;

    public function render() {
        $fi = ($this->fecha_ini != '' ? Carbon::parse($this->fecha_ini)->format('Y-m-d').' 00:00:00'
                                      : Carbon::parse(Carbon::now())->format('Y-m-d').' 00:00:00');

        $ff = ($this->fecha_fin != '' ? Carbon::parse($this->fecha_fin)->format('Y-m-d').' 23:59:59'
                                      : Carbon::parse(Carbon::now())->format('Y-m-d').' 23:59:59');

        $ventas = Renta::leftjoin('tarifas as t', 't.id', 'rentas.tarifa_id')
					->leftjoin('users as u', 'u.id', 'rentas.user_id')
					->select('rentas.*', 't.costo as tarifa','t.descripcion as vehiculo', 'u.nombre as usuario')
					->whereBetween('rentas.created_at',[$fi , $ff ])
					->paginate($this->pagination);

        $total = Renta::whereBetween('rentas.created_at',[$fi , $ff ])->where('status','CERRADO')->sum('total');

        return view('livewire.reportes.component-ventas-por-fechas', [
            'info' => $ventas,
        	'sumaTotal' => $total
        ]);
    }

    // Paginación
    public function updatingSearch(): void{
        $this->gotoPage(1);
    }
}

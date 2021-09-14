<?php

namespace App\Http\Livewire;

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

use Livewire\Component;
use App\Models\User;
use App\Models\Renta;
use App\Models\Caja;
use Carbon\Carbon;

class CortesController extends Component
{
    public $fecha, $user;
	public $ventas,$entradas,$salidas,$balance;

    public function mount(){
		$this->ventas   = 0;
		$this->entradas = 0;
		$this->salidas  = 0;
		$this->balance = ($this->ventas + $this->entradas) - $this->salidas;
	}

    public function render() {

        $users = User::where('tipo','<>','Cliente')
		    ->select('nombre','id')
		    ->get();

        return view('livewire.cortes.component-cortes', [
            'users' => $users
        ]);
    }

    public function Balance() {
        if($this->user == 0){
			$this->ventas  = Renta::whereDate('created_at', Carbon::today())
			        ->sum('total');

			$this->entradas = Caja::whereDate('created_at', Carbon::today())
			        ->where('tipo','Ingreso')->sum('monto');

			$this->salidas  = Caja::whereDate('created_at', Carbon::today())
			        ->where('tipo','<>','Ingreso')->sum('monto');
		}else{
            $this->ventas  = Renta::where('user_id', $this->user)
			        ->whereDate('created_at', Carbon::today())
			        ->sum('total');

			$this->entradas = Caja::where('user_id', $this->user)
                    ->whereDate('created_at', Carbon::today())
			        ->where('tipo','Ingreso')->sum('monto');

			$this->salidas  = Caja::where('user_id', $this->user)
                    ->whereDate('created_at', Carbon::today())
			        ->where('tipo','<>','Ingreso')->sum('monto');
        }

		$this->balance = ($this->ventas + $this->entradas) - $this->salidas;
    }

    public function Consulta() {
		$fi = Carbon::parse($this->fecha)->format('Y-m-d').' 00:00:00';
		$ff = Carbon::parse($this->fecha)->format('Y-m-d').' 23:59:59';

        if($this->user == 0) {
			$this->ventas  = Renta::whereBetween('created_at',[$fi , $ff ])
                ->sum('total');

            $this->entradas = Caja::whereBetween('created_at', [$fi , $ff ] )
                ->where('tipo','Ingreso')
                ->sum('monto');

            $this->salidas  = Caja::whereBetween('created_at', [$fi , $ff ] )
                ->where('tipo','<>','Ingreso')
                ->sum('monto');
		}else{
            $this->ventas  = Renta::where('user_id', $this->user)
			    ->whereBetween('created_at',[$fi , $ff ])
			    ->sum('total');

			$this->entradas = Caja::where('user_id', $this->user)
                ->whereBetween('created_at', [$fi , $ff ] )
			    ->where('tipo','Ingreso')->sum('monto');

			$this->salidas  = Caja::where('user_id', $this->user)
                ->whereBetween('created_at', [$fi , $ff ] )
			    ->where('tipo','<>','Ingreso')->sum('monto');
        }

		$this->balance = ($this->ventas + $this->entradas) - $this->salidas;
    }

    public function PrintCorte($ventas,$entradas,$salidas,$balance) {
		$nombreImpresora = "eQual";
		$connector = new WindowsPrintConnector($nombreImpresora);
		$impresora = new Printer($connector);

		$impresora->setJustification(Printer::JUSTIFY_CENTER);
		$impresora->setTextSize(2, 2);
		$impresora->text("ESTACIONAMIENTO\n Y PENSIÓN PLAZA\n");
		$impresora->setTextSize(1, 1);
		$impresora->text("** Corte de Caja ** \n\n");

		$impresora->setJustification(Printer::JUSTIFY_LEFT);
		$impresora->text("=============================================\n");
		$impresora->text("Usuario: ". ($this->user == null ? 'Todos' : $this->user) ."\n");
		$impresora->text("Fecha: ". ($this->fecha == null ? date('m/d/Y h:i:s a', time()) :  Carbon::parse($this->fecha)->format('d-m-Y') ) ."\n");
		$impresora->text("--------------- " ."\n");
		$impresora->text("Ventas: $". number_format($ventas,2) ." \n"  );
		$impresora->text("Entradas: $". number_format($entradas,2) ." \n");
		$impresora->text("Salidas: $". number_format($salidas,2) ." \n");
		$impresora->text('Balance: $'. number_format($balance,2) ." \n");
		$impresora->text("=============================================\n");

		$impresora->feed(3);
		$impresora->cut();
		$impresora->close();

		//$this->emit('printCorte', $this->fecha, $this->user, $ventas,$entradas,$salidas,$balance);
	}

    protected $listeners = [
		'info2PrintCorte'   => 'PrintCorte'
	];
}

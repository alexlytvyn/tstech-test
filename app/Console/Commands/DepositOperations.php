<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Deposit;
use App\Operation;
use Illuminate\Support\Facades\DB;

class DepositOperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposit:operations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accrual of interest and withdrawal of commissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
			$deposits = Deposit::all();
			foreach ($deposits as $deposit) {
				if ((date('Y-m-d') > date('Y-m-d', strtotime($deposit->dep_start_date))) && (date('Y-m-d') <= date('Y-m-d', strtotime($deposit->dep_end_date)))) { // Якщо договір дійсний на даний час
					if (((date('d') == date('d', strtotime($deposit->dep_start_date))) &&
					(date('d', strtotime($deposit->dep_start_date)) < 31)) || // Нараховуємо відсотки щомісячно відповідно до числа місяця відкриття депозиту
					((date('d') == date('t')) && (date('d', strtotime($deposit->dep_start_date)) == 31))) { // Якщо депозит відкрито 31-го числа, нараховуємо відсотки у останній день місяця
						Deposit::where('id', $deposit->id)->update([
							'curr_balance' => DB::raw('curr_balance * (1+(percent/12/100))') // Нараховуємо відсотки за депозитом відповідно до умов задачі
						]);

						$operation = Operation::create([ // Відповідну операцію записуємо у базу даних
							'deposit_id' => $deposit->id,
							'operation_datetime' => date('Y-m-d H:i:s'),
							'operation_amount' => $deposit->curr_balance * $deposit->percent / 12 / 100,
							'type' => 1 // Тип 1 - нарахування відсотків за депозитом
						]);
					}

					if (date('d') == 01) { // Кожного першого числа знімаємо комісію відповідно до залишку на рахунку
						if ($deposit->curr_balance < 1000) {
							if (($deposit->curr_balance * 0.05) > 50) {
								$commission = $deposit->curr_balance * 0.05;
							} else {
								$commission = 50;
							}
						}

						if ($deposit->curr_balance >= 1000 && $deposit->curr_balance <= 10000) {
							$commission = $deposit->curr_balance * 0.06;
						}

						if ($deposit->curr_balance > 10000) {
							if (($deposit->curr_balance * 0.07) > 5000) {
								$commission = 5000;
							} else {
								$commission = $deposit->curr_balance * 0.07;
							}
						}

						if ((date('m') - date('m', strtotime($deposit->dep_start_date)) == 1) || (date('m') - date('m', strtotime($deposit->dep_start_date)) == -11)) { // Якщо депозит відкрито попереднього місяця
							$commission = $commission * (date('t', strtotime($deposit->dep_start_date)) - date('d', strtotime($deposit->dep_start_date)) + 1) / date('t', strtotime($deposit->dep_start_date)); // Комісію знімаємо частково
						}

						Deposit::where('id', $deposit->id)->update([
							'curr_balance' => DB::raw("curr_balance - $commission") // Змінюємо поточний баланс після зняття комісії
						]);

						$operation = Operation::create([ // Записуємо фін. операцію у базу даних
							'deposit_id' => $deposit->id,
							'operation_datetime' => date('Y-m-d H:i:s'),
							'operation_amount' => $commission,
							'type' => 2 // Тип 2 - зняття комісії
						]);
					}
				}
			}
    }
}

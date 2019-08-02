@extends('layouts.app')

@section('content')
	<div class="container">
		<h1 class="text-center">Банк Василя Петровича</h1>
		<p>У процесі виконання завдання було додано консольну команду 'php artisan deposit:operations', при виконанні якої нараховуються
		відсотки за депозитом та знімаються комісії відповідно до завдання. Крім того, у відповідній таблиці бази даних ведеться облік даних
		фінансових операцій. Обробник цієї команди міститься у файлі за адресою:
		 '/app/Console/Commands/DepositOperations.php'. Команда зареєстрована у файлі '/app/Console/Kernel.php'.</p>
		<p>Отже, у панелі керування Cron достатньо додати щоденний запуск команди 'php artisan deposit:operations >> /dev/null 2>&1'.</p>
	</div>
@endsection

<?php
require_once 'speedtest.php';

// Name des gebuchten Tarifs
$tarif = 'Vodafone Red 400';

// Download warning level (MBit/s)
$dlw = 200;

// Download fail level (MBit/s)
$dlf = 80;

// Upload warning level (MBit/s)
$ulw = 10;

// Upload fail level (MBit/s)
$ulf = 5;


// Create speedtest
$speedtest = new Speedtest(__DIR__ . '/logs', $dlw, $dlf, $ulw, $ulf);
?>

<html>
<head>
<title>Speedtests <?php echo $tarif; ?></title>
<link href="bootstrap.css" rel="stylesheet">
</head>

<body>
	<div class="container">
		<h1 class="display-5 my-4">Speedtests <?php echo $tarif; ?></h1>
		<h3 class="mt-3">Einleitung</h3>
		<p class="lead">Die nachfolgenden Speedtests werden in unterschiedlichen Abstände an den jeweiligen Servern ausgeführt. Es werden in der Regel mehrere Server abgefragt, damit ein kurzzeitiger Engpass an einem Server nicht das Ergebnis der gesamten Messung zu stark verfälschen kann. Aus allen Speedtests wird der mit dem höchsten Downloadspeed für die Beurteilung herangezogen.</p>
		<h3 class="mt-3">Tests</h3>
		<p><span class="text-success">Downloadspeed &gt; <?php echo $dlw; ?> MBit/s (success)</span> | <span class="text-warning">Downloadspeed &lt; <?php echo $dlw; ?> MBit/s aber &gt; <?php echo $dlf; ?> MBit/s (Warn)</span> | <span class="text-danger">Downloadspeed &lt; <?php echo $dlf; ?> MBit/s (Fail)</span></p>
		<div id="accordion" role="tablist">
		
		<?php
        /* @var $day Testday */
        foreach ($speedtest as $day) {
    
            $runs = $day->getRuns();
    
            ?>

			<div class="card">
				<div class="card-header" role="tab" id="headingOne">
					<h5 class="mb-0">
						<a data-toggle="collapse" href="#collapse<?php echo $day->getDay(); ?>" aria-expanded="true" aria-controls="collapse<?php echo $day->getDay(); ?>"><?php echo $day->getDay(); ?></a>
					</h5>
					<strong>Summary: <?php echo count($runs); ?> Tests | Download: &empty; <?php echo round($day->getDownload(),2); ?> MBit/s | Warn: <?php echo $day->getWarnings();?> | Fail: <?php echo $day->getFails(); ?></strong>
				</div>
				<div id="collapse<?php echo $day->getDay(); ?>" class="collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="">
						<table class="table table-bordered table-sm table-striped">
							<thead>
								<tr>
									<th>Run</th>
									<th>Time</th>
									<th>Ping</th>
									<th>DL</th>
									<th>UL</th>
									<th colspan="2">Server</th>
								</tr>
							</thead>
							<?php
    
                            $testrun = 0;
                            
                            foreach ($runs as $key => $result) {
                                
                                $testrun ++;
                                
                                $test = $result['best'];
                             ?>
							<tbody>
								<tr class="text-<?php echo $test->color->dl; ?>">
									<td><?php echo $testrun; ?></td>
									<td><?php echo date('H:i:s', $test->time); ?></td>
									<td><?php echo $test->ping; ?> ms</td>
									<td><?php echo round($test->download,2); ?> MBit/s</td>
									<td><?php echo round($test->upload,2); ?> MBit/s</td>
									<td><?php echo $test->server->name;?> (<?php echo $test->server->sponsor; ?>)</td>
									<td><a data-toggle="collapse" href="#<?php echo $day->getDay() . '-results-' . $key; ?>">Alle Tests anzeigen (<?php echo count($result['tests']);?>)</a></td>
								</tr>
							</tbody>

							<tbody class="collapse table-secondary" id="<?php echo $day->getDay() . '-results-' . $key; ?>">								
							
							<?php foreach ($result['tests'] as $test) { ?>
   							
   								<tr class="table-<?php echo $test->color->dl; ?>">
   									<td>&nbsp;</td>
									<td><?php echo date('H:i:s', $test->time); ?></td>
									<td><?php echo $test->ping; ?> ms</td>
									<td><?php echo round($test->download,2); ?> MBit/s</td>
									<td><?php echo round($test->upload,2); ?> MBit/s</td>
									<td colspan="2"><?php echo $test->server->name;?> (<?php echo $test->server->sponsor; ?>)</td>
								</tr>
						
							<?php } ?>

							</tbody>

						<?php } ?>

						</table>
					</div>
				</div>
			</div>

			<?php } ?>

		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

</body>

</html>


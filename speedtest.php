<?php

class Speedtest implements IteratorAggregate
{

    private $data = [];

    private $days = [];

    public function __construct($dir_logs, int $dlw, int $dlf, int $ulw, int $ulf)
    {
        $this->spider($dir_logs, $this->data);
        
        krsort($this->data);
        
        foreach ($this->data as $day => $tests_today) {
            
            $testday = new Testday($day);
            ksort($tests_today);
            
            foreach ($tests_today as $timestamp => $tests) {
                
                if (empty($tests)) {
                    continue;
                }
                
                $testanalyzer = new TestAnalyzer($tests);
                $testanalyzer->setDownloadWarnLimit($dlw);
                $testanalyzer->setDownloadFailLimit($dlf);
                $testanalyzer->setUploadWarnLimit($ulw);
                $testanalyzer->setUploadFailLimit($ulf);
                $testanalyzer->analyze();
                
                $testday->addTestrun($testanalyzer->getBest(), $testanalyzer->getResult());
            }
            
            $this->days[] = $testday;
        }
    }

    // benötigte Funktion des IteratorAggregate Interface
    public function getIterator()
    {
        return new ArrayIterator($this->days);
    }

    private function spider($dir, &$storage)
    {
        $handle = opendir($dir);
        
        while ($file = readdir($handle)) {
            
            if ($file != "." && $file != "..") {
                
                $filename = $dir . "/" . $file;
                
                if (is_dir($filename)) {
                    
                    $key = date('Y-m-d', $file);
                    $storage[$key][$file] = [];
                    
                    $this->spider($filename, $storage[$key][$file]);
                }
                
                if (is_file($filename)) {
                    
                    $log = json_decode(file_get_contents($filename));
                    
                    if (empty($log) or empty($log->download)) {
                        continue;
                    }
                    
                    $time = new DateTime($log->timestamp);
                    
                    $time->modify('+1 Hour');
                    
                    $log->time = $time->getTimestamp();
                    
                    $storage[$log->time] = $log;
                }
            }
        }
    }
}

class Testday
{

    private $day;

    private $ul = 0;

    private $dl = 0;

    private $runs = [];

    private $fails = 0;

    private $warnings = 0;

    public function __construct($day)
    {
        $this->day = $day;
    }

    public function addTestrun(stdClass $best, array $tests)
    {
        $this->ul += $best->upload;
        $this->dl += $best->download;
        
        switch (true) {
            case $best->color->dl == 'danger':
                $this->fails ++;
                break;
            case $best->color->dl == 'warning':
                $this->warnings ++;
                break;
        }
        
        $this->runs[] = [
            'best' => $best,
            'tests' => $tests
        ];
    }

    public function getDownload()
    {
        return $this->dl / count($this->runs);
    }

    public function getUpload()
    {
        return $this->ul / count($this->runs);
    }

    public function getRuns(): array
    {
        return $this->runs;
    }

    public function getDay()
    {
        return $this->day;
    }
    
    public function getFails() {
        return $this->fails;
    }
    
    public function getWarnings() {
        return $this->warnings;
    }
}

class TestAnalyzer
{

    /**
     *
     * @var array
     */
    private $limits = [
        'ul' => [
            'w' => 10,
            'f' => 5
        ],
        'dl' => [
            'w' => 200,
            'f' => 40
        ]
    ];

    /**
     *
     * @var array
     */
    private $tests = [];

    /**
     *
     * @var stdClass
     */
    private $best;

    /**
     *
     * @var array
     */
    private $warnings = [
        'dl' => 0,
        'ul' => 0
    ];

    /**
     *
     * @var array
     */
    private $fails = [
        'dl' => 0,
        'ul' => 0
    ];

    public function __construct(array $tests)
    {
        ksort($tests);
        
        $this->tests = $tests;
    }

    public function setDownloadWarnLimit(int $limit)
    {
        $this->limits['dl']['w'] = $limit;
    }

    public function setDownloadFailLimit(int $limit)
    {
        $this->limits['dl']['f'] = $limit;
    }

    public function setUploadWarnLimit(int $limit)
    {
        $this->limits['ul']['w'] = $limit;
    }

    public function setUploadFailLimit(int $limit)
    {
        $this->limits['ul']['f'] = $limit;
    }

    public function getDownloadFails(): int
    {
        return $this->fails['dl'];
    }


    public function getResult(): array
    {
        return $this->tests;
    }

    public function getBest()
    {
        return $this->best;
    }

    public function analyze()
    {
        foreach ($this->tests as $test) {
            
            // Skip failed tests
            if (empty($test->download)) {
                continue;
            }
            
            $analyze = [
                'dl' => 'download',
                'ul' => 'upload'
            ];
            
            $test->color = new stdClass();
            
            foreach ($analyze as $key => $property) {
                
                // Convert Bit into MBit
                $test->{$property} = $test->{$property} / 1000000;
                
                // Analyze speed
                switch (true) {
                    case $test->{$property} < $this->limits[$key]['f']:
                        $color = 'danger';
                        break;
                    case $test->{$property} < $this->limits[$key]['w']:
                        $color = 'warning';
                        break;
                    default:
                        $color = 'success';
                        break;
                }
                
                $test->color->{$key} = $color;
            }
            
            // determine best test
            if (empty($this->best)) {
                $this->best = $test;
            }
            
            if ($test->download > $this->best->download) {
                $this->best = $test;
            }
        }
    }
}

function getResultStorage(): array
{
    return [
        'tests' => 0,
        'dl' => [
            'r' => 0,
            'w' => 0,
            'f' => 0
        ],
        'ul' => [
            'r' => 0,
            'w' => 0,
            'f' => 0
        ]
    ];
}

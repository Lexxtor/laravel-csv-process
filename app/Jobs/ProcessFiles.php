<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessFiles implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $filePath;
    private $fileSize;
    private $fileLines;
    private $fileLinesProcessed = 0;
    private $logEveryLine = 300; // после обработки скольки строк писать в лог сколько строк обработано

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Processing file: ' . $this->filePath);
        $stream = Storage::readStream($this->filePath);

        if (!$this->fileLines) {
            $this->fileLines = $this->countFileLines($stream);
            Log::info($this->filePath . ' lines count: ' . $this->fileLines);
        }

        while ($lineData = fgetcsv($stream)) {
            if (!isset($lineData[0], $lineData[1])) {
                Log::info($this->filePath . ' invalid line: ' . ($this->fileLinesProcessed+1));
                continue;
            }
            $data = [
                'state' => $lineData[1],
                'updated_at' => now(),
            ];
            if (!empty($lineData[2])) $data['amount'] = $lineData[2];
            if (!empty($lineData[3])) $data['comment'] = $lineData[3];

            DB::table('services')
                ->where(['id' => $lineData[0]])
                ->update($data);
            $this->fileLinesProcessed++;

            if (!($this->fileLinesProcessed % $this->logEveryLine)) {
                Log::info($this->filePath . ' lines processed: ' . $this->fileLinesProcessed . ' / ' . $this->fileLines);
            }
        }

        if ($this->fileLinesProcessed % $this->logEveryLine) {
            Log::info($this->filePath . ' lines processed: ' . $this->fileLinesProcessed . ' / ' . $this->fileLines);
        }

        fclose($stream);
    }

    private function countFileLines($stream): int
    {
        $lines = 0;

        while (!feof($stream)) {
            $lines += substr_count(
                fread($stream, 8192),
                "\n");
        }

        rewind($stream);

        return $lines;
    }
}

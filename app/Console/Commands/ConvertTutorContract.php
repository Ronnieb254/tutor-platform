<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertTutorContract extends Command
{
    protected $signature = 'contract:generate {--source= : Path to the DOCX file}';
    protected $description = 'Convert the Tutor Services Agreement DOCX to PDF and store it';

    public function handle(): int
    {
        $source = $this->option('source') 
            ?? storage_path('app/contracts/tutor-agreement.docx');
        
        $output = storage_path('app/contracts/tutor-agreement.pdf');

        if (!file_exists($source)) {
            $this->error("Source file not found: {$source}");
            $this->line("Please upload the DOCX file to: {$source}");
            return 1;
        }

        // Try LibreOffice conversion (available on most Linux servers)
        $cmd = "libreoffice --headless --convert-to pdf --outdir " 
            . escapeshellarg(storage_path('app/contracts')) 
            . " " . escapeshellarg($source);

        exec($cmd, $output_lines, $exitCode);

        if ($exitCode === 0) {
            // LibreOffice outputs with original filename — rename if needed
            $generatedFile = storage_path('app/contracts/tutor-agreement.pdf');
            if (file_exists($generatedFile)) {
                $this->info("Contract PDF generated successfully at: {$generatedFile}");
                return 0;
            }
        }

        $this->error("Conversion failed. Please convert the DOCX manually to PDF and place it at:");
        $this->line($output);
        return 1;
    }
}

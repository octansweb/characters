<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class Polly {
    public static function generateSpeech($text, $fileName = null, $gender = 'Female', $voiceId = null, $engine = 'generative')
    {
        // Determine the voice based on gender if no specific voiceId is provided
        if (is_null($voiceId)) {
            $voiceId = $gender === 'Male' 
                ? config('voices.defaultMale', 'Matthew') 
                : config('voices.defaultFemale', 'Danielle');
        }

        // Ensure the file name ends with .mp3
        $fileName = $fileName ?? uniqid('speech_', true) . '.mp3';
        if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'mp3') {
            $fileName .= '.mp3';
        }

        // Temporary file path for the synthesized audio
        $tempFilePath = sys_get_temp_dir() . '/' . $fileName;

        // Construct the AWS Polly CLI command with NTTS engine
        $command = sprintf(
            'aws polly synthesize-speech --engine %s --output-format mp3 --voice-id %s --text %s %s',
            escapeshellarg($engine),
            escapeshellarg($voiceId),
            escapeshellarg($text),
            escapeshellarg($tempFilePath)
        );

        // Execute the command
        exec($command, $output, $returnVar);

        // Check if the command was successful
        if ($returnVar === 0) {
            // Store the file on the public disk (ensuring a publicly accessible URL) and delete the temp file
            $storedFilePath = Storage::disk('public')->putFileAs('audio', $tempFilePath, $fileName);
            unlink($tempFilePath); // Remove the temporary file

            // Return the public URL to the stored file
            return Storage::disk('public')->url($storedFilePath);
        } else {
            return "An error occurred: " . implode("\n", $output);
        }
    }
}

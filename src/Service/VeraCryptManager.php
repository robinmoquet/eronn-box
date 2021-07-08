<?php


namespace App\Service;


use Ramsey\Uuid\Uuid;

class VeraCryptManager
{

    public function createContainer(array $tmpOptions)
    {
        $options = [
            "encryption" => "AES",
            "filesystem" => "FAT",
            "volume-type" => "normal",
            "hash" => "sha512",
            "random-source" => "tmp",
            "non-interactive" => ""
        ];
        $options = array_merge($tmpOptions, $options);

        $randomPath = $this->createRandomFile();
        $options["random-source"] = $randomPath;

        $command = $this->getCommandLineForCreate($options);
        $res = exec($command, $output);
        unlink($randomPath);
        dd($res);
    }

    private function getCommandLineForCreate(array $options): string
    {
        $command = "~/bin/veracrypt -t --create {$options['path']}";
        unset($options['path']);
        foreach ($options as $key => $value) {
            if ($value === "") $command .= " --{$key}";
            else $command .= " --{$key}={$value}";
        }
        return $command;
    }

    private function createRandomFile(): string
    {
        $path = dirname(dirname(__DIR__)) . '/var/cache/random/' . Uuid::uuid4();
        file_put_contents($path, random_bytes(320));
        return $path;
    }
}

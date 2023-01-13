<?php

namespace Uniter1\UniterLaravel\Controller\Console\Cli;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;
use Uniter1\UniterLaravel\LaravelRequester;

class RegisterUniterUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uniter1:register {email} {password}';
    protected $name = 'uniter1:register';

    /**
     * The console command description.
     */
    protected $description = 'Generate phpunit test';

    /**
     * Execute the console command.
     */
    public function handle(LaravelRequester $laravelRequester): ?int
    {
        try {
            $email = $this->argument('email');
            $password = $this->argument('password');

            $validator = Validator::make(
                ['email'    => $email, 'password' => $password],
                [
                    'email'    => 'required|string|email|max:255',
                    'password' => ['required', 'string'],
                ]);

            if (!is_string($email) || !is_string($password)) {
                throw new ValidationException($validator);
            }

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $code = $laravelRequester->register($email, $password);

            if (0 === $code) {
                $this->info('User registered. Access token in your email. Put it in .env file - UNITER1_ACCESS_TOKEN');
            }
        } catch (ValidationException $e) {
            $this->error("Command Validation Error: \n".$this->listMessages($e->errors()));

            return 1;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $report = $laravelRequester->getReport();
        foreach ($report->getErrors() as $message) {
            $this->error($message);
        }
        foreach ($report->getInfos() as $message) {
            $this->info($message);
        }

        return $code;
    }
}

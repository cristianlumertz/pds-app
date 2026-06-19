<?php

namespace App\Console\Commands;

use App\Mail\NewsletterPromotion;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:send
                            {--title= : Título da campanha}
                            {--body= : Texto do corpo da campanha}
                            {--cta-text= : Texto do botão de ação}
                            {--cta-url= : URL do botão de ação}
                            {--coupon= : Código do cupom promocional}
                            {--coupon-desc= : Descrição do cupom promocional}
                            {--dry-run : Apenas mostra quantos assinantes seriam impactados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispara e-mail de newsletter para todos os assinantes opt-in verificados';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('dry-run')) {
            $subscriberCount = User::newsletterSubscribers()->count();
            $this->info("Simulação concluída: {$subscriberCount} assinantes seriam impactados.");

            return self::SUCCESS;
        }

        $requiredOptions = [
            'title' => '--title',
            'body' => '--body',
            'cta-text' => '--cta-text',
            'cta-url' => '--cta-url',
        ];

        foreach ($requiredOptions as $option => $label) {
            if (trim((string) $this->option($option)) === '') {
                $this->error("A opção {$label} é obrigatória.");

                return self::INVALID;
            }
        }

        $subscriberCount = User::newsletterSubscribers()->count();

        if ($subscriberCount === 0) {
            $this->warn('Nenhum assinante opt-in com e-mail verificado foi encontrado.');

            return self::SUCCESS;
        }

        if (! $this->confirm("Enviar para {$subscriberCount} assinantes? (yes/no)", false)) {
            $this->warn('Envio cancelado.');

            return self::SUCCESS;
        }

        $campaignTitle = (string) $this->option('title');
        $campaignBody = (string) $this->option('body');
        $ctaText = (string) $this->option('cta-text');
        $ctaUrl = (string) $this->option('cta-url');
        $couponCode = $this->nullableOption('coupon');
        $couponDescription = $this->nullableOption('coupon-desc');

        if (! Str::startsWith($ctaUrl, ['http://', 'https://'])) {
            $ctaUrl = url($ctaUrl);
        }

        $sentCount = 0;
        $progressBar = $this->getOutput()->createProgressBar($subscriberCount);
        $progressBar->start();

        User::newsletterSubscribers()->chunk(100, function ($users) use (
            $campaignTitle,
            $campaignBody,
            $ctaText,
            $ctaUrl,
            $couponCode,
            $couponDescription,
            &$sentCount,
            $progressBar,
        ): void {
            foreach ($users as $user) {
                $mail = (new NewsletterPromotion(
                    campaignTitle: $campaignTitle,
                    campaignBody: $campaignBody,
                    ctaText: $ctaText,
                    ctaUrl: $ctaUrl,
                    couponCode: $couponCode,
                    couponDescription: $couponDescription,
                ))->with('notifiable', $user);

                Mail::to($user)->queue($mail);

                $sentCount++;
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);
        $this->info("Newsletter enviada para {$sentCount} destinatários.");

        return self::SUCCESS;
    }

    private function nullableOption(string $name): ?string
    {
        $value = trim((string) $this->option($name));

        return $value !== '' ? $value : null;
    }
}

<?php

namespace App\Mail;

use App\Models\Truyenchon\TruyenchonChapter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class TruyenchonDownloadChapterMail extends Mailable
{
    use Queueable, SerializesModels;

    private TruyenchonChapter $chapterModel;
    private string $pdfFile;

    public function __construct(TruyenchonChapter $chapterModel, string $pdfFile)
    {
        $this->chapterModel = $chapterModel;
        $this->pdfFile = $pdfFile;
    }

    public function build(): self
    {
        $subject = config('app.name').' - Truyenchon - '.$this->chapterModel->title.' / '.$this->chapterModel->chapter;
        $user = Auth::user();

        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($subject)
            ->markdown('emails.truyenchon.downloadChapter')
            ->with(
                [
                    'chapter' => $this->chapterModel,
                    'name' => $user->name ?? 'Guest',
                ]
            )
            ->attach($this->pdfFile);
    }
}

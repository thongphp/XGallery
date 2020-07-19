<?php

namespace App\Mail;

use App\Models\Truyenchon\TruyenchonChapterModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class TruyenchonDownloadChapterMail extends Mailable
{
    use Queueable, SerializesModels;

    private TruyenchonChapterModel $chapterModel;
    private string $pdfFile;

    public function __construct(TruyenchonChapterModel $chapterModel, string $pdfFile)
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
            ->markdown('truyenchon.email.downloadChapter')
            ->with(
                [
                    'chapter' => $this->chapterModel,
                    'name' => $user ? $user->name : 'Guest',
                ]
            )
            ->attach($this->pdfFile);
    }
}

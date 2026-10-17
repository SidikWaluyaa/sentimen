<?php

namespace App\Services\NLP;

use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

class IndonesianTextPreprocessor
{
    /** @var \Sastrawi\Stemmer\StemmerInterface */
    protected $stemmer;

    /** @var \Sastrawi\StopWordRemover\StopWordRemover */
    protected $stopwordRemover;

    protected array $slangDict = [
        'yg' => 'yang',
        'dgn' => 'dengan',
        'tdk' => 'tidak',
        'bgt' => 'banget',
        'klo' => 'kalau',
        'jg' => 'juga',
        'udh' => 'sudah',
        'nya' => '',
        'gw' => 'saya',
        'bener' => 'benar',
        'ga' => 'tidak',
        'gak' => 'tidak',
        'nggak' => 'tidak',
        'gk' => 'tidak',
        'tp' => 'tapi',
        'sdh' => 'sudah',
        'sy' => 'saya',
        'd' => 'di',
        'krn' => 'karena',
        'aja' => 'saja',
        'sampe' => 'sampai',
        'blm' => 'belum',
    ];

    public function __construct()
    {
        $stemmerFactory = new StemmerFactory();
        $this->stemmer = $stemmerFactory->createStemmer();

        $stopwordFactory = new StopWordRemoverFactory();
        $this->stopwordRemover = $stopwordFactory->createStopWordRemover();
    }

    public function preprocess(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // 1. Case folding
        $text = mb_strtolower($text, 'UTF-8');

        // 2. Remove URL
        $text = preg_replace('/https?:\/\/\S+|www\.\S+/', ' ', $text);

        // 3. Remove Mention
        $text = preg_replace('/@\w+/', ' ', $text);

        // 4. Remove Hashtag
        $text = preg_replace('/#\w+/', ' ', $text);

        // 5. Remove numbers
        $text = preg_replace('/\d+/', ' ', $text);

        // 6. Only keep English/Indonesian letters and spaces
        $text = preg_replace('/[^a-z\s]/', ' ', $text);

        // 7. Remove extra spaces
        $text = trim(preg_replace('/\s+/', ' ', $text));

        // 8. Slang & Typo Normalization
        $words = explode(' ', $text);
        $normalizedWords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') continue;
            $normalizedWords[] = $this->slangDict[$word] ?? $word;
        }
        $text = implode(' ', array_filter($normalizedWords));

        // 9. Stopword removal (Sastrawi)
        $text = $this->stopwordRemover->remove($text);

        // 10. Stemming (Sastrawi Nazief-Adriani algorithm)
        $text = $this->stemmer->stem($text);

        return trim(preg_replace('/\s+/', ' ', $text));
    }
}

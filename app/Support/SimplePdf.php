<?php

namespace App\Support;

class SimplePdf
{
    private array $commands = [];

    public function text(float $x, float $y, string $text, int $size = 12, bool $bold = false, string $align = 'left'): self
    {
        $font = $bold ? 'F2' : 'F1';
        $text = self::escape($text);

        if ($align === 'right') {
            $width = $this->approxTextWidth($text, $size);
            $x = max(20, $x - $width);
        } elseif ($align === 'center') {
            $width = $this->approxTextWidth($text, $size);
            $x = max(20, $x - ($width / 2));
        }

        $this->commands[] = sprintf(
            "BT /%s %d Tf %.2f %.2f Td (%s) Tj ET",
            $font,
            $size,
            $x,
            $y,
            $text
        );

        return $this;
    }

    public function fillRect(float $x, float $y, float $w, float $h, float $gray = 0.92): self
    {
        $g = number_format(max(0, min(1, $gray)), 3, '.', '');
        $this->commands[] = sprintf(
            "q %s g %.2f %.2f %.2f %.2f re f Q",
            $g,
            $x,
            $y,
            $w,
            $h
        );

        return $this;
    }

    public function line(float $x1, float $y1, float $x2, float $y2, float $width = 0.8, float $gray = 0.85): self
    {
        $g = number_format(max(0, min(1, $gray)), 3, '.', '');
        $this->commands[] = sprintf(
            "q %s G %.2f w %.2f %.2f m %.2f %.2f l S Q",
            $g,
            $width,
            $x1,
            $y1,
            $x2,
            $y2
        );

        return $this;
    }

    /**
     * @param array<int, array{size?:int,bold?:bool,text:string,space_after?:int}> $rows
     */
    public static function fromRows(array $rows): string
    {
        $builder = new self();
        $y = 810;

        foreach ($rows as $row) {
            $text = (string) ($row['text'] ?? '');
            $size = (int) ($row['size'] ?? 12);
            $bold = (bool) ($row['bold'] ?? false);
            $spaceAfter = (int) ($row['space_after'] ?? 6);
            $wrapped = explode("\n", wordwrap($text, 96, "\n", true));

            foreach ($wrapped as $line) {
                if ($y < 40) {
                    break 2;
                }
                $builder->text(45, $y, $line, $size, $bold);
                $y -= (int) max($size + 4, 14);
            }

            $y -= $spaceAfter;
        }

        return $builder->render();
    }

    public function render(): string
    {
        $content = implode("\n", $this->commands);
        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Count 1 /Kids [3 0 R] >>";
        $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> /Contents 4 0 R >>";
        $objects[4] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        $objects[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $id => $obj) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $obj . "\nendobj\n";
        }

        $xrefPos = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 " . $count . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i < $count; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }

        $pdf .= "trailer << /Size " . $count . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPos . "\n%%EOF";

        return $pdf;
    }

    private function approxTextWidth(string $text, int $size): float
    {
        $length = strlen($text);
        return $length * ($size * 0.52);
    }

    private static function escape(string $text): string
    {
        $ascii = preg_replace('/[^\x20-\x7E]/', ' ', $text) ?? '';
        $ascii = str_replace('\\', '\\\\', $ascii);
        $ascii = str_replace('(', '\(', $ascii);
        $ascii = str_replace(')', '\)', $ascii);

        return $ascii;
    }
}

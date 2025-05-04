<?

namespace Services\Xls;

interface ReaderInterface
{
    public function loadXls($file, string $type): void;

    public function getData(): array;
}
<?

namespace Services\Xls;

interface ReaderInterface
{
    public function loadXls($file): void;

    public function getData(): array;
}
<?

namespace Services\Xls;

class Reader implements ReaderInterface
{
    private $sheet;

    public function loadXls($file, string $type): void
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
        $reader->setReadDataOnly(true);

        $this->sheet = $reader->load($file);

        var_dump($this->sheet);
    }


    public function getData(): array
    {
        return [];
    }
}
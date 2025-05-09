<?

namespace Services\Xls;

class Reader implements ReaderInterface
{
    private $sheet;

    public function loadXls($file): void
    {
        $testAgainstFormats = [
            \PhpOffice\PhpSpreadsheet\IOFactory::READER_XLS,
            \PhpOffice\PhpSpreadsheet\IOFactory::READER_HTML,
        ];
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);

        $this->sheet = $reader->load($file, 0, $testAgainstFormats);

    }


    public function getData(): array
    {
        $data = [];
        foreach($this->sheet->getActiveSheet()->toArray() as $k => $line) {
            if($k === 0) {
                continue;
            }

            array_push($data, $line);

        }

        return $data;
    }
}
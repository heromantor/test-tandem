<?php
namespace App;

use App\Entity\RealtyOffer;
use DateTimeImmutable;
use XMLWriter;

/**
 * Экспортер предложений в xml формат
 */
class XmlExporter
{
    /**
     * Кодировка
     */
    const DEFAULT_XML_ENCODING = 'utf-8';

    /**
     * Namespace
     */
    const DEFAULT_FEED_NS = 'http://webmaster.yandex.ru/schemas/feed/realty/2010-06';

    /**
     * Едининцы измерения площади по умолчанию
     */
    const DEFAULT_AREA_UNITS = 'кв. м';

    /**
     * Экспортрует набор предложений в файл.
     *
     * @param string $outputPath Путь до файла
     * @param RealtyOffer[] $offers Набор предложений
     *
     * @return self
     */
    public function exportToFile(string $outputPath, iterable $offers): self
    {
        $writer = new XMLWriter();

        $writer->openURI($outputPath);
        $writer->setIndent(true);
        $writer->setIndentString(str_repeat(' ', 4));

        $writer->startDocument('1.0', self::DEFAULT_XML_ENCODING);

        $writer->startElement('realty-feed');
            $writer->writeAttribute('xmlns', self::DEFAULT_FEED_NS);

            $writer->writeElement('generation-date', $this->formatDate(new DateTimeImmutable()));

            foreach ($offers as $realty) {
                $this->dumpOffer($writer, $realty);
            }

        $writer->endElement();


        $writer->flush();

        return $this;
    }

    /**
     * Генерирует одно предложение в xml
     *
     * @param XMLWriter $writer
     * @param RealtyOffer $offer
     */
    private function dumpOffer(XMLWriter $writer, RealtyOffer $offer): void
    {
        $realty = $offer->getRealty();
        $client = $offer->getClient();

        $writer->startElement('offer');
            $writer->writeAttribute('internal-id', $realty->getId());
            $writer->writeElement('type', 'продажа');
            $writer->writeElement('property-type', 'жилая');
            $writer->writeElement('category', 'квартира');
            $writer->writeElement('creation-date', $this->formatDate($offer->getCreatedAt()));

            if(null !== $offer->getUpdatedAt()) {
                $writer->writeElement('last-update-date', $this->formatDate($offer->getUpdatedAt()));
            }

            $writer->startElement('area');
                $writer->writeElement('value', round($realty->getArea(), 0));
                $writer->writeElement('unit', self::DEFAULT_AREA_UNITS);
            $writer->endElement();

            $writer->startElement('location');
                $writer->writeElement('address', $realty->getAddress());
            $writer->endElement();

            $writer->writeElement('rooms', $realty->getRooms());
            $writer->writeElement('floor', $realty->getFloor());

            $writer->startElement('sales-agent');
                $writer->writeElement('name', $client->getName());
                $writer->writeElement('phone', $client->getPhone());
                $writer->writeElement('address', $client->getAddress());
            $writer->endElement();

            $writer->startElement('photos');
                foreach ($realty->getPhotos() as $photo) {
                    $writer->startElement('photo');
                        $writer->writeElement('url', $photo->getUrl());
                    $writer->endElement();
                }
            $writer->endElement();

        $writer->endElement(); // offer
    }

    /**
     * Форматурет дату
     *
     * @param DateTimeImmutable $date Дата
     * @return string Отформатированная дата
     */
    private function formatDate(DateTimeImmutable $date): string
    {
        return $date->format('Y-m-d\TH:i:sP');
    }

}
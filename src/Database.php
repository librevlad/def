<?php


namespace Librevlad\Def;


use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Collection;

class Database {

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $def;

    public function __construct() {

        $def = floadcsv( __DIR__ . '/../database/DEF-9xx.csv', [
            'code',
            'start',
            'stop',
            'count',
            'operator',
            'region',
        ], ';' );

        $regionMap = [
            'г. Москва и Московская область'                                  => 'Московская Область',
            'г. Санкт-Петербург и Ленинградская область'                      => 'Ленинградская область',
            'г. Санкт - Петербург и Ленинградская область'                    => 'Ленинградская область',
            'Корякский округ|Камчатский край'                                 => 'Камчатский край',
            'Чувашская Республика - Чувашия'                                  => 'Чувашская Республика',
            'г. Москва'                                                       => 'Московская Область',
            'Республика Саха /Якутия/'                                        => 'Якутия',
            'г. Сочи|Краснодарский край'                                      => 'Краснодарский край',
            'Республика Кабардино-Балкарская'                                 => 'Кабардино-Балкарская Республика',
            'Республика Карачаево-Черкесская'                                 => 'Карачаево-Черкесская Республика',
            'Республика Крым и г. Севастополь'                                => 'Республика Крым',
            'г. Севастополь'                                                  => 'Республика Крым',
            'р-ны Абзелиловский и Белорецкий|р-ны Абзелиловский и Белорецкий' => 'Республика Башкортостан',
            'Чувашская Республика - Чувашия'                                  => 'Чувашская Республика',
            'Республика Удмуртская'                                           => 'Удмуртская Республика',
            'Сургутский район и г. Сургут'                                    => 'Ханты - Мансийский - Югра АО',
            'г. Норильск|Красноярский край'                                   => 'Красноярский край',
            'г. Владимир|Владимирская обл.'                                   => 'Владимирская обл.',
            'г. Кострома|р-н Костромской|Костромская обл.'                    => 'Костромская обл.',
            'г. Псков|Псковская обл.'                                         => 'Псковская обл.',
        ];

        $this->def = collect( $def )->map( function ( $v ) use ( $regionMap ) {
            $v[ 'region' ] = Arr::get( $regionMap, $v[ 'region' ], $v[ 'region' ] );
            $v[ 'region' ] = trim( str_replace( 'обл.', 'область', $v[ 'region' ] ) );

            return $v;
        } );

    }

    public function def() {
        return $this->def;
    }

    public function operators() {
        return $this->def->pluck( 'operator' )->unique()->values();
    }

    public function regions() {
        return $this->def->pluck( 'region' )->unique()->values();
    }

    public function operatorByPhoneNumber( $phone ) {
        return Arr::get( $this->findByPhoneNumber( $phone ), 'operator' );
    }

    public function regionByPhoneNumber( $phone ) {
        return Arr::get( $this->findByPhoneNumber( $phone ), 'region' );
    }

    public function findByPhoneNumber( $phone ) {

        $code = substr( $phone, 1, 3 );
        $rest = substr( $phone, 4 );

        return $this->def->where( 'code', $code )->where( 'start', '<', $rest )->last();

    }

}

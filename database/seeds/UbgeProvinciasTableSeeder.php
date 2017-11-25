<?php

use Illuminate\Database\Seeder;

class UbgeProvinciasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ubge_provincias')->insert([
          //=== CHUQUISACA ===
            [
                "departamento_id" => "5",
                "codigo"          => "0108",
                "nombre"          => "BELISARIO BOETO",
            ],
            [
                "departamento_id" => "5",
                "codigo"          => "0105",
                "nombre"          => "HERNANDO SILES",
            ],
            [
                "departamento_id" => "5",
                "codigo"          => "0103",
                "nombre"          => "JAIME ZUDAÑEZ",
            ],
            [
                "departamento_id" => "5",
                "codigo"          => "0102",
                "nombre"          => "JUANA AZURDUY DE PADILLA",
            ],
            [
                "departamento_id" => "5",
                "codigo"          => "0110",
                "nombre"          => "LUIS CALVO",
            ],
            [
                "departamento_id" => "5",
                "codigo"          => "0107",
                "nombre"          => "NOR CINTI",
            ],
            [
                "departamento_id" => "5",
                "codigo"          => "0101",
                "nombre"          => "OROPEZA",
            ],
            [
                "departamento_id" => "5",
                "codigo"          => "0109",
                "nombre"          => "SUD CINTI",
            ],
            [
                "departamento_id" => "5",
                "codigo"          => "0104",
                "nombre"          => "TOMINA",
            ],
            [
                "departamento_id" => "5",
                "codigo"          => "0106",
                "nombre"          => "YAMPARAEZ",
            ],
          //=== LA PAZ ===
            [
                "departamento_id" => "1",
                "codigo"          => "0215",
                "nombre"          => "ABEL ITURRALDE",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0213",
                "nombre"          => "AROMA",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0216",
                "nombre"          => "BAUTISTA SAAVEDRA",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0220",
                "nombre"          => "CARANAVI",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0204",
                "nombre"          => "ELIODORO CAMACHO",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0207",
                "nombre"          => "FRANZ TAMAYO",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0219",
                "nombre"          => "GENERAL JOSE MANUEL PANDO",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0218",
                "nombre"          => "GUALBERTO VILLARROEL",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0208",
                "nombre"          => "INGAVI",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0210",
                "nombre"          => "INQUISIVI",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0206",
                "nombre"          => "LARECAJA",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0209",
                "nombre"          => "LOAYZA",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0212",
                "nombre"          => "LOS ANDES",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0217",
                "nombre"          => "MANCO KAPAC",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0205",
                "nombre"          => "MUÑECAS",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0201",
                "nombre"          => "PEDRO DOMINGO MURILLO",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0214",
                "nombre"          => "NOR YUNGAS",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0202",
                "nombre"          => "OMASUYOS",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0203",
                "nombre"          => "PACAJES",
            ],
            [
                "departamento_id" => "1",
                "codigo"          => "0211",
                "nombre"          => "SUD YUNGAS",
            ],
          //=== COCHABAMBA ===
            [
                "departamento_id" => "4",
                "codigo"          => "0305",
                "nombre"          => "ARANI",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0306",
                "nombre"          => "ARQUE",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0303",
                "nombre"          => "AYOPAYA",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0315",
                "nombre"          => "BOLIVAR",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0302",
                "nombre"          => "CAMPERO",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0307",
                "nombre"          => "CAPINOTA",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0312",
                "nombre"          => "CARRASCO",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0301",
                "nombre"          => "CERCADO",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0310",
                "nombre"          => "CHAPARE",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0304",
                "nombre"          => "ESTEBAN ARCE",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0308",
                "nombre"          => "GERMAN JORDAN",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0313",
                "nombre"          => "MIZQUE",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0314",
                "nombre"          => "PUNATA",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0309",
                "nombre"          => "QUILLACOLLO",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0311",
                "nombre"          => "TAPACARI",
            ],
            [
                "departamento_id" => "4",
                "codigo"          => "0316",
                "nombre"          => "TIRAQUE",
            ],
          //=== ORURO ===
            [
                "departamento_id" => "2",
                "codigo"          => "0409",
                "nombre"          => "ATAHUALLPA",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0403",
                "nombre"          => "CARANGAS",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0401",
                "nombre"          => "CERCADO",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0402",
                "nombre"          => "EDUARDO AVAROA",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0408",
                "nombre"          => "LADISLAO CABRERA",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0405",
                "nombre"          => "LITORAL",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0415",
                "nombre"          => "PUERTO DE MEJILLONES",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0416",
                "nombre"          => "NOR CARANGAS",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0407",
                "nombre"          => "PANTALEON DALENCE",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0406",
                "nombre"          => "POOPO",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0404",
                "nombre"          => "SAJAMA",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0413",
                "nombre"          => "SAN PEDRO DE TOTORA",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0410",
                "nombre"          => "SAUCARI",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0414",
                "nombre"          => "SEBASTIAN PAGADOR",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0412",
                "nombre"          => "SUD CARANGAS",
            ],
            [
                "departamento_id" => "2",
                "codigo"          => "0411",
                "nombre"          => "TOMAS BARRON",
            ],
          //=== POTOSI ===
            [
                "departamento_id" => "3",
                "codigo"          => "0507",
                "nombre"          => "ALONZO DE IBAÑEZ",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0512",
                "nombre"          => "ANTONIO QUIJARRO",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0505",
                "nombre"          => "CHARCAS",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0504",
                "nombre"          => "CHAYANTA",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0503",
                "nombre"          => "CORNELIO SAAVEDRA",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0514",
                "nombre"          => "DANIEL CAMPOS",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0516",
                "nombre"          => "ENRIQUE BALDIVIESO",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0513",
                "nombre"          => "GENERAL BERNARDINO BILBAO RIOJA",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0511",
                "nombre"          => "JOSE MARIA LINARES",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0515",
                "nombre"          => "MODESTO OMISTE",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0506",
                "nombre"          => "NOR CHICHAS",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0509",
                "nombre"          => "NOR LIPEZ",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0502",
                "nombre"          => "RAFAEL BUSTILLO",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0508",
                "nombre"          => "SUD CHICHAS",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0510",
                "nombre"          => "SUD LIPEZ",
            ],
            [
                "departamento_id" => "3",
                "codigo"          => "0501",
                "nombre"          => "TOMAS FRIAS AMETLLER",
            ],
          //=== TARIJA ===
            [
                "departamento_id" => "6",
                "codigo"          => "0602",
                "nombre"          => "ANICETO ARCE",
            ],
            [
                "departamento_id" => "6",
                "codigo"          => "0606",
                "nombre"          => "BURNET O CONNOR",
            ],
            [
                "departamento_id" => "6",
                "codigo"          => "0601",
                "nombre"          => "CERCADO",
            ],
            [
                "departamento_id" => "6",
                "codigo"          => "0605",
                "nombre"          => "EUSTAQUIO MENDEZ",
            ],
            [
                "departamento_id" => "6",
                "codigo"          => "0603",
                "nombre"          => "GRAN CHACO",
            ],
            [
                "departamento_id" => "6",
                "codigo"          => "0604",
                "nombre"          => "JOSE MARIA AVILEZ",
            ],
          //=== SANTA CRUZ ===
            [
                "departamento_id" => "9",
                "codigo"          => "0701",
                "nombre"          => "ANDRES IBAÑEZ",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0712",
                "nombre"          => "ANGEL SANDOVAL",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0705",
                "nombre"          => "CHIQUITOS",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0707",
                "nombre"          => "CORDILLERA",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0709",
                "nombre"          => "FLORIDA",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0714",
                "nombre"          => "GERMAN BUSCH",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0715",
                "nombre"          => "GUARAYOS",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0704",
                "nombre"          => "ICHILO",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0703",
                "nombre"          => "JOSE MIGUEL DE VELASCO",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0713",
                "nombre"          => "MANUEL MARIA CABALLERO",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0711",
                "nombre"          => "ÑUFLO DE CHAVEZ",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0710",
                "nombre"          => "OBISPO SANTISTEBAN",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0706",
                "nombre"          => "SARA",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0708",
                "nombre"          => "VALLEGRANDE",
            ],
            [
                "departamento_id" => "9",
                "codigo"          => "0702",
                "nombre"          => "WARNES",
            ],
          //=== BENI ===
            [
                "departamento_id" => "8",
                "codigo"          => "0802",
                "nombre"          => "ANTONIO VACA DIEZ",
            ],
            [
                "departamento_id" => "8",
                "codigo"          => "0801",
                "nombre"          => "CERCADO",
            ],
            [
                "departamento_id" => "8",
                "codigo"          => "0803",
                "nombre"          => "MARISCAL JOSE BALLIVIAN SEGUROLA",
            ],
            [
                "departamento_id" => "8",
                "codigo"          => "0808",
                "nombre"          => "ITENEZ",
            ],
            [
                "departamento_id" => "8",
                "codigo"          => "0807",
                "nombre"          => "MAMORE",
            ],
            [
                "departamento_id" => "8",
                "codigo"          => "0806",
                "nombre"          => "MARBAN",
            ],
            [
                "departamento_id" => "8",
                "codigo"          => "0805",
                "nombre"          => "MOXOS",
            ],
            [
                "departamento_id" => "8",
                "codigo"          => "0804",
                "nombre"          => "YACUMA",
            ],
          //=== PANDO ===
            [
                "departamento_id" => "7",
                "codigo"          => "0904",
                "nombre"          => "ABUNA",
            ],
            [
                "departamento_id" => "7",
                "codigo"          => "0905",
                "nombre"          => "FEDERICO ROMAN",
            ],
            [
                "departamento_id" => "7",
                "codigo"          => "0903",
                "nombre"          => "MADRE DE DIOS",
            ],
            [
                "departamento_id" => "7",
                "codigo"          => "0902",
                "nombre"          => "MANURIPI",
            ],
            [
                "departamento_id" => "7",
                "codigo"          => "0901",
                "nombre"          => "NICOLAS SUAREZ",
            ]
        ]);
    }
}

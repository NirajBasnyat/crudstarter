<?php


namespace Niraj\CrudStarter\Traits;


trait CommonCode
{
    protected function resolve_migration($fields = ''): string
    {
        $fieldLookUp = [
            'inc' => 'increments',
            'str' => 'string',
            'int' => 'integer',
            'uint' => 'unsignedInteger',
            'tinyint' => 'tinyInteger',
            'utinyint' => 'unsignedTinyInteger',
            'smallint' => 'smallInteger',
            'usmallint' => 'unsignedSmallInteger',
            'mediumint' => 'mediumInteger',
            'umediumint' => 'unsignedMediumInteger',
            'bigint' => 'bigInteger',
            'ubigint' => 'unsignedBigInteger',
            'txt' => 'text',
            'tinytext' => 'tinyText',
            'mediumtext' => 'mediumText',
            'longtext' => 'longText',
            'bool' => 'boolean',
            'fid' => 'foreignId',
        ];

        $migrationSchema = '';

        if ($fields != '') {

            $fieldsArray = explode(' ', $fields);

            $data = array();

            $iteration = 0;
            foreach ($fieldsArray as $field) {
                $fieldArraySingle = explode(':', $field);
                $data[$iteration]['name'] = trim($fieldArraySingle[0]);
                $data[$iteration]['type'] = trim($fieldArraySingle[1]);

                $iteration++;
            }

            foreach ($data as $item) {
                if (isset($fieldLookUp[$item['type']])) {
                    $type = $fieldLookUp[$item['type']];

                    $migrationSchema .= "\$table->" . $type . "('" . $item['name'] . "');" . PHP_EOL . '            ';
                } else {
                    $migrationSchema .= "\$table->" . $item['type'] . "('" . $item['name'] . "');" . PHP_EOL . '            ';
                }
            }
        }

        return $migrationSchema;
    }

    protected function resolve_request($fields = ''): string
    {
        $validationLookUp = [
            'int' => 'required|integer',
            'uint' => 'required|integer',
            'integer' => 'required|integer',
            'tinyint' => 'required|integer',
            'utinyint' => 'required|integer',
            'tinyInteger' => 'required|integer',
            'smallint' => 'required|integer',
            'usmallint' => 'required|integer',
            'smallInteger' => 'required|integer',
            'mediumint' => 'required|integer',
            'umediumint' => 'required|integer',
            'mediumInteger' => 'required|integer',
            'bigint' => 'required|integer',
            'ubigint' => 'required|integer',
            'bigInteger' => 'required|integer',
            'str' => 'required|string',
            'string' => 'required|string',
            'txt' => 'required|string',
            'text' => 'required|string',
            'mediumtext' => 'required|string|min:5',
            'mediumText' => 'required|string|min:5',
            'longtext' => 'required|string|min:10',
            'longText' => 'required|string|min:10',
            'bool' => 'required|boolean',
            'boolean' => 'required|boolean',
            'date' => 'required|date'
        ];


        $validationRules = '';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            foreach ($data as $item) {
                if (isset($validationLookUp[$item['type']])) {
                    $type = $validationLookUp[$item['type']];

                    $validationRules .= "'" . $item['name'] . "'" . '=>' . "'" . $type . "'," . PHP_EOL . '            ';
                } else {
                    $validationRules .= "'" . $item['name'] . "'" . '=>' . "'required'," . PHP_EOL . '            ';
                }
            }
        }

        return $validationRules;
    }


    protected function resolve_fields(string $fields): array
    {
        $fieldsArray = explode(' ', $fields);

        $data = array();

        $iteration = 0;
        foreach ($fieldsArray as $field) {
            $fieldArraySingle = explode(':', $field);
            $data[$iteration]['name'] = trim($fieldArraySingle[0]);
            $data[$iteration]['type'] = trim($fieldArraySingle[1]);

            $iteration++;
        }
        return $data;
    }

}

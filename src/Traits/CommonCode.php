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


    protected function resolve_create_test_fields($fields = ''): string
    {
        $testFieldLookUp = [
            'str' => "test string",
            'string' => "test string",
            'int' => 1,
            'integer' => 1,
            'uint' => 1,
            'tinyint' => 12,
            'tinyInteger' => 12,
            'utinyint' => 12,
            'smallint' => 123,
            'smallInteger' => 123,
            'usmallint' => 123,
            'mediumint' => 1234,
            'mediumInteger' => 1234,
            'umediumint' => 1234,
            'bigint' => 123456,
            'bigInteger' => 123456,
            'ubigint' => 123456,
            'txt' => "some long string here",
            'text' => "some long string here",
            'tinytext' => "some tiny long string here",
            'mediumtext' => "some medium long string here",
            'mediumText' => "some medium long string here",
            'longtext' => "some super long string here",
            'longText' => "some super long string here",
            'bool' => true,
        ];

        $createTestFields = '';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            $createTestFields = $this->resolve_fields_for_test($data, $testFieldLookUp, $createTestFields);
        }

        return $createTestFields;
    }


    protected function resolve_update_test_fields($fields = ''): string
    {
        $testUpdateFieldLookUp = [
            'str' => "test string updated",
            'string' => "test string updated",
            'int' => 10,
            'integer' => 10,
            'uint' => 10,
            'tinyint' => 120,
            'tinyInteger' => 120,
            'utinyint' => 120,
            'smallint' => 1230,
            'smallInteger' => 1230,
            'usmallint' => 1230,
            'mediumint' => 12340,
            'mediumInteger' => 12340,
            'umediumint' => 12340,
            'bigint' => 1234560,
            'bigInteger' => 1234560,
            'ubigint' => 1234560,
            'txt' => "some long string updated here",
            'text' => "some long string updated here",
            'tinytext' => "some tiny long string updated here",
            'mediumtext' => "some medium long string updated here",
            'mediumText' => "some medium long string updated here",
            'longtext' => "some super long string updated here",
            'longText' => "some super long string updated here",
            'bool' => false,
        ];

        $updateTestFields = '';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            $updateTestFields = $this->resolve_fields_for_test($data, $testUpdateFieldLookUp, $updateTestFields);
        }

        return $updateTestFields;
    }

    protected function resolve_first_of_update_field($fields = ''): string
    {
        $firstField = '';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            $firstField = $data[0]['name'];
        }

        return $firstField;
    }

    //extracted functions

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


    protected function resolve_fields_for_test(array $data, array $testFieldLookUp, string $createTestFields): string
    {
        foreach ($data as $item) {
            if (isset($testFieldLookUp[$item['type']]) && is_numeric($testFieldLookUp[$item['type']])) {

                $type = $testFieldLookUp[$item['type']];

                $createTestFields .= "'" . $item['name'] . "'" . '=>' . $type . "," . PHP_EOL . '            ';

            } elseif (isset($testFieldLookUp[$item['type']]) && !is_numeric($testFieldLookUp[$item['type']])) {

                $type = $testFieldLookUp[$item['type']];

                $createTestFields .= "'" . $item['name'] . "'" . "=>'" . $type . "'," . PHP_EOL . '            ';
            } else {
                $createTestFields .= "'" . $item['name'] . "'" . '=>' . "'enter value here'," . PHP_EOL . '            ';
            }
        }
        return $createTestFields;
    }

}

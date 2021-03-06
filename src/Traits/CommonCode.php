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
                //use in array
                if (in_array($item['name'], ['image', 'images', 'img', 'pic', 'pics', 'picture', 'pictures', 'avatar', 'photo', 'photos', 'gallery'])) {
                    $type = $fieldLookUp[$item['type']];
                    $migrationSchema .= "\$table->" . $type . "('" . $item['name'] . "')->nullable();" . PHP_EOL . '            ';
                } elseif (isset($fieldLookUp[$item['type']]) && ($fieldLookUp[$item['type']] == 'bool' || $fieldLookUp[$item['type']] == 'boolean')) {
                    $type = $fieldLookUp[$item['type']];
                    $migrationSchema .= "\$table->" . $type . "('" . $item['name'] . "')->default(0);" . PHP_EOL . '            ';
                } elseif ($item['type'] == 'select') {
                    $migrationSchema .= "\$table->integer('" . $item['name'] . "');" . PHP_EOL . '            ';
                } elseif (isset($fieldLookUp[$item['type']])) {
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
            'bool' => 'boolean',
            'boolean' => 'boolean',
            'date' => 'required|date'
        ];


        $validationRules = '';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            foreach ($data as $item) {
                if (in_array($item['name'], ['image', 'images', 'img', 'pic', 'pics', 'picture', 'pictures', 'avatar', 'photo', 'photos', 'gallery'])) {
                    $validationRules .= "'" . $item['name'] . "'" . '=>' . "'image|nullable'," . PHP_EOL . '            ';
                } elseif (isset($validationLookUp[$item['type']])) {
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
            'bool' => 1,
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
            'bool' => 0,
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

    // -----------------------------------------------for create form fields

    protected function get_fields_for_create($fields = ''): string
    {
        $fieldsForCreate = '';
        $parent_class = 'form-group';
        $class = 'form-control';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            foreach ($data as $item) {

                if (in_array($item['name'], ['image', 'images', 'img', 'pic', 'pics', 'picture', 'pictures', 'avatar', 'photo', 'photos', 'gallery'])) {
                    $fieldsForCreate .= '<x-form.input type="file"' . ' label="' . ucfirst($item['name']) . '" id="' . $item['name'] . '" name="' . $item['name'] . '" alt="image"/>' . PHP_EOL;
                } elseif (in_array($item['type'], ['str', 'string'])) {
                    $fieldsForCreate .= '<x-form.input type="text"' . ' label="' . ucfirst($item['name']) . '" id="' . $item['name'] . '" name="' . $item['name'] . '" value="{{old(\'' . $item['name'] . '\')}}"/>' . PHP_EOL;
                } elseif ($item['type'] == 'select') {
                    $fieldsForCreate .= '<x-form.select name="' . $item['name'] . '" label="' . ucfirst($item['name']) . '" :options="' . $item['options'] . '"/>' . PHP_EOL;
                } elseif (in_array($item['type'], ['txt', 'text', 'tinytext', 'tinyText', 'mediumtext', 'mediumText', 'longtext', 'longText'])) {
                    $fieldsForCreate .= '<x-form.textarea label="' . ucfirst($item['name']) . '" id="' . $item['name'] . '" name="' . $item['name'] . '" value="' . '{{old(\'' . $item['name'] . '\')}}" rows="5" cols="5" /> ' . PHP_EOL;
                } elseif (in_array($item['type'], ['bool', 'boolean'])) {
                    $fieldsForCreate .= '<x-form.checkbox label="' . ucfirst($item['name']) . '" id="' . $item['name'] . '" name="' . $item['name'] . '" value="1"' . ' class="form-check-input" />' . PHP_EOL;
                } else {
                    $fieldsForCreate .= '<x-form.input type="number"' . ' label="' . ucfirst($item['name']) . '" id="' . $item['name'] . '" name="' . $item['name'] . '" value="{{old(\'' . $item['name'] . '\')}}"/>' . PHP_EOL;
                }
            }

            return $fieldsForCreate;
        }
    }

    // ---------------------------------------------------------------------

    protected function get_fields_for_edit($fields = '', $snake_cased_var): string
    {
        $fieldsForEdit = '';
        $parent_class = 'form-group';
        $class = 'form-control';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            foreach ($data as $item) {

                if (in_array($item['name'], ['image', 'images', 'img', 'pic', 'pics', 'picture', 'pictures', 'avatar', 'photo', 'photos', 'gallery'])) {
                    $fieldsForEdit .= '<x-form.input type="file"' . ' label="' . ucfirst($item['name']) . '" id="' . $item['name'] . '" name="' . $item['name'] . '" alt="image"/>' . PHP_EOL;
                } elseif (in_array($item['type'], ['str', 'string'])) {
                    $fieldsForEdit .= '<x-form.input type="text"' . ' label="' . ucfirst($item['name']) . '" id="' . $item['name'] . '" name="' . $item['name'] . '" value="{{$' . $snake_cased_var . '->' . $item['name'] . '}}"/>' . PHP_EOL;
                } elseif ($item['type'] == 'select') {
                    $fieldsForEdit .= '<x-form.select name="' . $item['name'] . '" label="' . ucfirst($item['name']) . '" :options="' . $item['options'] . '" model="{{$' . $snake_cased_var . '->' . $item['name'] . '}}"/>' . PHP_EOL;
                } elseif (in_array($item['type'], ['txt', 'text', 'tinytext', 'tinyText', 'mediumtext', 'mediumText', 'longtext', 'longText'])) {
                    $fieldsForEdit .= '<x-form.textarea label="' . ucfirst($item['name']) . '" id="' . $item['name'] . '" name="' . $item['name'] . '" value="{{$' . $snake_cased_var . '->' . $item['name'] . '}}" rows="5" cols="5" /> ' . PHP_EOL;
                } elseif (in_array($item['type'], ['bool', 'boolean'])) {
                    $fieldsForEdit .= '<x-form.checkbox label="' . ucfirst($item['name']) . '" id="' . $item['name'] . '" name="' . $item['name'] . '" value="1" class="form-check-input" isEditMode="yes" :isChecked="$' . $snake_cased_var . '->' . $item['name'] . ' ? \'checked\' : \'\' "/>' . PHP_EOL;
                } else {
                    $fieldsForEdit .= '<x-form.input type="number"' . ' label="' . ucfirst($item['name']) . '" id="' . $item['name'] . '" name="' . $item['name'] . '" value="{{$' . $snake_cased_var . '->' . $item['name'] . '}}"/>' . PHP_EOL;
                }

                // dump($item['name']);//name
                //dump($item['type']);//str
            }

            return $fieldsForEdit;
        }
    }

    // ---------------------------------------------------------------------

    protected function get_fields_create_and_edit($fields = '', $snake_cased_var): string
    {
        $fieldsForCreateAndEdit = '';
        $parent_class = 'form-group';
        $class = 'form-control';
        $space = '                        ';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            foreach ($data as $item) {

                if ($item['name'] == 'image' || $item['name'] == 'img' || $item['name'] == 'pic' || $item['name'] == 'picture' || $item['name'] == 'avatar' || $item['name'] == 'photo') {
                    $fieldsForCreateAndEdit .= $space . '<div class="' . $parent_class . '">' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '<label for="' . $item['name'] . '">' . ucfirst($item['name']) . '</label>' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '<input type="image" class="' . $class . '" name="' . $item['name'] . '" id="' . $item['name'] . '" alt="image">' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '</div>' . PHP_EOL . PHP_EOL;
                } elseif ($item['type'] == 'str' || $item['type'] == 'string') {
                    $fieldsForCreateAndEdit .= $space . '<div class="' . $parent_class . '">' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '<label for="' . $item['name'] . '">' . ucfirst($item['name']) . '</label>' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '<input type="text" class="' . $class . '" name="' . $item['name'] . '" id="' . $item['name'] . '" value="{{ old(\'' . $snake_cased_var . '\') ?:' . '(isset($' . $snake_cased_var . ') ? $' . $snake_cased_var . '->' . $item['name'] . ' : "") }}">' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '</div>' . PHP_EOL . PHP_EOL;
                } elseif ($item['type'] == 'text' || $item['type'] == 'txt') {
                    $fieldsForCreateAndEdit .= $space . '<div class="' . $parent_class . '">' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '<label for="' . $item['name'] . '">' . ucfirst($item['name']) . '</label>' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '<textarea class="' . $class . '" name="' . $item['name'] . '" id="' . $item['name'] . '" rows="5" cols="5">{{ old(\'' . $snake_cased_var . '\') ?:' . '(isset($' . $snake_cased_var . ') ? $' . $snake_cased_var . '->' . $item['name'] . ' : "")}}</textarea>' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '</div>' . PHP_EOL . PHP_EOL;
                } else {
                    $fieldsForCreateAndEdit .= $space . '<div class="' . $parent_class . '">' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '<label for="' . $item['name'] . '">' . ucfirst($item['name']) . '</label>' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '<input type="number" class="' . $class . '" name="' . $item['name'] . '" id="' . $item['name'] . '" value="{{ old(\'' . $snake_cased_var . '\') ?:' . '(isset($' . $snake_cased_var . ') ? $' . $snake_cased_var . '->' . $item['name'] . ' : "") }}">' . PHP_EOL . $space;
                    $fieldsForCreateAndEdit .= '</div>' . PHP_EOL . PHP_EOL;
                }
            }

            return $fieldsForCreateAndEdit;
        }
    }

    // ---------------------------------------------------------------------FOR INDEX BLADE

    protected function get_rows_for_index($fields = '', $modelNameSingularLowerCase = ''): string
    {
        // dump($item['name']);//name
        //dump($item['type']);//str

        $rowsForEdit = '';
        $space = '                        ';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            foreach ($data as $item) {
                $rowsForEdit .= '<td>{{$' . $modelNameSingularLowerCase . '->' . $item['name'] . '}}</td>' . PHP_EOL . $space . PHP_EOL . $space;
            }

            return $rowsForEdit;
        }
    }

    protected function get_thead_for_index($fields = ''): string
    {
        $theadRows = '';
        $space = '                        ';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            foreach ($data as $item) {
                $theadRows .= '<th>' . ucfirst($item['name']) .
                    '</th>' . $space . PHP_EOL . $space;
            }
        }
        return $theadRows;
    }

    // ---------------------------------------------------------------------


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

            if ($fieldArraySingle[1] == 'select') {
                $options = trim($fieldArraySingle[2]);
                $options = str_replace('options=', '', $options);
                $optionsArray = explode(',', $options);
                $commaSeparetedString = implode("', '", $optionsArray);
                $options = "['$commaSeparetedString']";
                $data[$iteration]['options'] = $options;
            }

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

<?php

namespace Niraj\CrudStarter\Traits;

use Illuminate\Support\Str;

trait ResolveCodeTrait
{
    # START CODE FOR CRUD----------------------------------------------------------------------------------------------------------

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
        $space = '            ';

        if ($fields != '') {
            $fieldsArray = explode(' ', $fields);

            foreach ($fieldsArray as $field) {
                [$name, $type] = explode(':', $field);
                $name = trim($name);
                $type = trim($type);

                if (in_array($name, config('crudstarter.image_fields'))) {
                    $type = $fieldLookUp[$type];
                    $migrationSchema .= "\$table->$type('$name')->nullable();".PHP_EOL.$space;
                } elseif (isset($fieldLookUp[$type]) && ($fieldLookUp[$type] == 'bool' || $fieldLookUp[$type] == 'boolean')) {
                    $type = $fieldLookUp[$type];
                    $migrationSchema .= "\$table->$type('$name')->default(1);".PHP_EOL.$space;
                } elseif ($name == 'slug') {
                    $migrationSchema .= "\$table->string('$name')->unique();".PHP_EOL.$space;
                } elseif ($type == 'select') {
                    $migrationSchema .= "\$table->integer('$name');".PHP_EOL.$space;
                } elseif (isset($fieldLookUp[$type])) {
                    $type = $fieldLookUp[$type];
                    $migrationSchema .= "\$table->$type('$name');".PHP_EOL.$space;
                } else {
                    $migrationSchema .= "\$table->$type('$name');".PHP_EOL.$space;
                }
            }
        }

        return $migrationSchema;
    }

    protected function resolve_request($fields = '', $type = 'store'): string
    {
        $validationLookUp = [
            'int' => config('crudstarter.default_validation').'|integer',
            'uint' => config('crudstarter.default_validation').'|integer',
            'integer' => config('crudstarter.default_validation').'|integer',
            'tinyint' => config('crudstarter.default_validation').'|integer',
            'utinyint' => config('crudstarter.default_validation').'|integer',
            'tinyInteger' => config('crudstarter.default_validation').'|integer',
            'smallint' => config('crudstarter.default_validation').'|integer',
            'usmallint' => config('crudstarter.default_validation').'|integer',
            'smallInteger' => config('crudstarter.default_validation').'|integer',
            'mediumint' => config('crudstarter.default_validation').'|integer',
            'umediumint' => config('crudstarter.default_validation').'|integer',
            'mediumInteger' => config('crudstarter.default_validation').'|integer',
            'bigint' => config('crudstarter.default_validation').'|integer',
            'ubigint' => config('crudstarter.default_validation').'|integer',
            'bigInteger' => config('crudstarter.default_validation').'|integer',
            'str' => config('crudstarter.default_validation').'|string',
            'string' => config('crudstarter.default_validation').'|string',
            'txt' => config('crudstarter.default_validation').'|string',
            'text' => config('crudstarter.default_validation').'|string',
            'mediumtext' => config('crudstarter.default_validation').'|string|min:5',
            'mediumText' => config('crudstarter.default_validation').'|string|min:5',
            'longtext' => config('crudstarter.default_validation').'|string|min:10',
            'longText' => config('crudstarter.default_validation').'|string|min:10',
            'bool' => 'boolean',
            'boolean' => 'boolean',
            'date' => config('crudstarter.default_validation').'|date'
        ];

        $validationRules = '';
        $imageValidation = $type === 'store' ? (config('crudstarter.image_required', false) === true ? 'required|image' : 'nullable|image') : 'nullable|image';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            foreach ($data as $item) {
                if (in_array($item['name'], config('crudstarter.image_fields'))) {
                    $validationRules .= "'".$item['name']."'".'=>'."'$imageValidation',".PHP_EOL.'            ';
                } elseif (isset($validationLookUp[$item['type']])) {
                    $type = $validationLookUp[$item['type']];
                    $validationRules .= "'".$item['name']."'".'=>'."'".$type."',".PHP_EOL.'            ';
                } else {
                    $validationRules .= "'".$item['name']."'".'=>'."'required',".PHP_EOL.'            ';
                }
            }
        }

        return $validationRules;
    }

    // --------------------------------------------------------------------- start of create and edit blade

    protected function get_fields_for_create($fields = ''): string
    {
        return $this->get_fields($fields);
    }

    protected function get_fields_for_edit($fields = '', $snake_cased_var): string
    {
        return $this->get_fields($fields, $snake_cased_var);
    }

    protected function get_fields_for_show($fields = '', $snake_cased_var): string
    {
        $fieldsData = '';

        if ($fields != '') {
            $data = $this->resolve_fields($fields);

            foreach ($data as $item) {
                $name = $item['name'];

                if (in_array($name, config('crudstarter.image_fields'))) {
                    $fieldsData .= '<div class="card-content mt-2">'.
                        '<b class="d-block text-uppercase text-14">'.$name.'</b>'.

                        '<x-table.table_image name="{{$'.$snake_cased_var.'->'.$name.' }}" url="{{$'.$snake_cased_var.'->image_path }}"/>';
                    '</div>'.PHP_EOL;
                } else {
                    $fieldsData .= '<div class="card-content mt-2">'.
                        '<b class="d-block text-uppercase text-14">'.$name.'</b>'.
                        '<span>{{$'.$snake_cased_var.'->'.$name.'}}</span>'.
                        '</div>'.PHP_EOL;
                }
            }
        }

        return $fieldsData;
    }

    protected function get_fields($fields, $snake_cased_var = null): string
    {
        $fieldsData = '';
        $counter = 0;

        if ($fields != '') {
            $data = $this->resolve_fields($fields);
            $cols_per_row = config('crudstarter.cols_per_row', 1);
            $colSpan = $cols_per_row > 1 ? floor(12 / $cols_per_row) : null;

            foreach ($data as $item) {
                $name = $item['name'];
                $label = ucfirst($name);
                $value = $snake_cased_var ? sprintf('{{$%s->%s}}', $snake_cased_var, $name) : '{{ old(\''.$name.'\') }}';
                $imagePath = $snake_cased_var ? ' url="{{$'.$snake_cased_var.'->image_path}}"' : null;

                // Start a new row if it's the first item or if the counter is $cols_per_row (meaning we've already added two items)
                if ($counter % $cols_per_row == 0 && $cols_per_row > 1) {
                    $fieldsData .= '<x-form.row>'.PHP_EOL;
                }

                $colSpanAttribute = $colSpan ? ' col="'.$colSpan.'"' : '';

                if (in_array($name, config('crudstarter.image_fields'))) {
                    $fieldsData .= '<x-form.input type="file" label="'.$label.'" id="'.$name.'" name="'.$name.'" alt="image" accept="image/*" onchange="previewThumb(\''.$name.'\''.',\''.$name.'-thumb\')"'.$colSpanAttribute.' />'.PHP_EOL;
                    $fieldsData .= '<x-form.preview for="'.$name.'" id="'.$name.'-thumb"'.$imagePath.'/>'.PHP_EOL;
                } elseif ($item['type'] == 'select') {
                    $options = $item['options'] ?? '[]';
                    $fieldsData .= '<x-form.select name="'.$name.'" label="'.$label.'" :options="'.$options.'" model="'.$value.'"'.$colSpanAttribute.'/>'.PHP_EOL;
                } elseif (in_array($item['type'], ['txt', 'text', 'tinytext', 'tinyText', 'mediumtext', 'mediumText', 'longtext', 'longText'])) {
                    $fieldsData .= '<x-form.textarea label="'.$label.'" id="'.$name.'" name="'.$name.'" value="'.$value.'" rows="5" cols="5"'.$colSpanAttribute.' />'.PHP_EOL;
                } elseif (in_array($item['type'], ['bool', 'boolean'])) {
                    $isChecked = $snake_cased_var ? sprintf('$%s->%s ? \'checked\' : \'\'', $snake_cased_var, $name) : '\'checked\'';
                    $fieldsData .= '<x-form.checkbox label="'.$label.'" id="'.$name.'" name="'.$name.'" value="1" class="form-check-input" isEditMode="yes" :isChecked="'.$isChecked.'"'.$colSpanAttribute.'/>'.PHP_EOL;
                } else {
                    $fieldsData .= '<x-form.input type="text" label="'.$label.'" id="'.$name.'" name="'.$name.'" value="'.$value.'"'.$colSpanAttribute.'/>'.PHP_EOL;
                }

                $counter++;

                // Close the row if it's the $cols_per_row item or if it's the last item and the counter is odd
                if (($counter % $cols_per_row == 0 || $counter == count($data)) && $cols_per_row > 1) {
                    $fieldsData .= '</x-form.row>'.PHP_EOL;
                }
            }
        }

        return $fieldsData;
    }


    // --------------------------------------------------------------------- start of index blade

    protected function get_rows_for_index($fields = '', $modelNameSingularLowerCase = ''): string
    {
        // dump($item['name']);->title
        // dump($item['type']);->str

        $rowsForIndex = '';
        $space = '                        ';

        if ($fields != '') {

            $data = $this->resolve_fields($fields);

            foreach ($data as $item) {
                if (in_array($item['name'], config('crudstarter.image_fields'))) {
                    $rowsForIndex .= '<x-table.table_image name="{{$'.$modelNameSingularLowerCase.'->'.$item['name'].' }}" url="{{$'.$modelNameSingularLowerCase.'->image_path }}"/>';
                } elseif ($item['name'] == 'status') {
                    $rowsForIndex .= '<x-table.switch :model="$'.$modelNameSingularLowerCase.'" />';
                } else {
                    $rowsForIndex .= '<x-table.td>{{$'.$modelNameSingularLowerCase.'->'.$item['name'].'}}</x-table.td>'.PHP_EOL.$space.PHP_EOL.$space;
                }
            }
        }
        return $rowsForIndex;
    }

    protected function get_thead_for_index($fields = ''): string
    {
        $theadRows = '';

        if ($fields != '') {
            $formattedFields = $this->get_fields_in_array_format($fields);
            $theadRows .= '<x-table.header :headers="['.implode(',', $formattedFields).', \'Actions\''.']" />';
        }

        return $theadRows;
    }

    protected function get_select_query_fields_for_index($fields = ''): string
    {
        $selectFields = '';

        if ($fields != '') {
            $formattedFields = $this->get_fields_in_array_format($fields);
            $selectFields .= "['id', ".implode(',', $formattedFields).']';
        }

        return $selectFields;
    }

    # CODE OF RELATIONSHIP -------------------------------------------------------------------------------------------------------

    protected function setRelationships(string $relations, string $folder_name = ''): string
    {
        $relationsLookUp = [
            'haso' => "hasOne",
            'hasm' => "hasMany",
            'belt' => "belongsTo",
            'belm' => "belongsToMany",
        ];

        $relationsCode = '';

        if ($relations != '') {
            $data = $this->resolve_fields($relations);
            $relationsCode = $this->get_code_for_relations($data, $relationsLookUp, $folder_name);
        }

        return $relationsCode;
    }

    protected function get_code_for_relations(array $data, array $relationsLookUp, string $folder_name): string
    {
        /*   "name" => "belongsTo"
             "type" => "User"
         */

        $relation_code = '';

        foreach ($data as $item) {
            $relation_code .= 'public function '.$item['type'].'(){'.PHP_EOL;

            if (isset($relationsLookUp[$item['name']])) {
                $_name = $relationsLookUp[$item['name']];
                $relation_code .= 'return $this->'.$_name.'('.Str::ucfirst(Str::singular($item['type'])).'::class);'.PHP_EOL;
            } else {
                $relation_code .= 'return $this->'.$item['name'].'('.Str::ucfirst(Str::singular($item['type'])).'::class);'.PHP_EOL;
            }

            $relation_code .= '}'.PHP_EOL.PHP_EOL;
        }

        return $relation_code;
    }

    # PHPUNIT CODE -------------------------------------------------------------------------------------------------------

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

    protected function field_has_status($fields = ''): bool
    {
        if ($fields != '') {
            $data = $this->resolve_fields($fields);
            foreach ($data as $item) {
                if ($item['name'] == 'status') {
                    return true;
                }
            }
        }
        return false;
    }

    protected function has_image_field($fields = ''): array
    {
        $imageField = [
            'hasImage' => false,
            'imageHelperCode' => '',
        ];

        if ($fields != '') {
            $data = $this->resolve_fields($fields);
            foreach ($data as $item) {
                if (in_array($item['name'], config('crudstarter.image_fields'))) {
                    $imageField['hasImage'] = true;
                    $imageField['imageHelperCode'] = "@include('_helpers.image_preview')";
                }
            }
        }

        return $imageField;
    }

    protected function get_status_change_helper_code($fields = ''): string
    {
        $hasStatus = $this->field_has_status($fields);
        return $hasStatus ? "@include('_helpers.status_change', ['url' => url('{{folderNameWithoutDot}}/status-change-{{modelNameSingularKebabCase}}')])" : '';
    }

    protected function get_status_change_method_code($name, $fields = ''): array
    {
        $statusChangeCode = [
            'statusChangeMethodCode' => '',
            'statusChangeTrait' => '',
            'statusChangeTraitImport' => '',
        ];
        $hasStatus = $this->field_has_status($fields);

        if ($hasStatus) {
            $statusChangeCode['statusChangeMethodCode'] = 'public function changeStatus(Request $request):void {'.PHP_EOL;
            $statusChangeCode['statusChangeMethodCode'] .= "\$this->changeItemStatus('".$name."',\$request->id,\$request->status);".PHP_EOL;
            $statusChangeCode['statusChangeMethodCode'] .= "}".PHP_EOL;

            $statusChangeCode['statusChangeTrait'] = 'use StatusTrait;';

            $statusChangeCode['statusChangeTraitImport'] = 'use App\Traits\StatusTrait;';
        }

        return $statusChangeCode;
    }

    # EXTRACTED FUNCTION ----------------------------------------------------------------------------------------------------------

    protected function get_fields_in_array_format($fields = ''): array
    {
        $arrayFormattedFields = '';

        if ($fields != '') {
            $data = $this->resolve_fields($fields);
            $arrayFormattedFields = collect($data)->pluck('name')->map(function ($item) {
                return "'".$item."'";
            })->toArray();
        }

        return $arrayFormattedFields;
    }

    protected function generate_controller_method_codes(string $modelName, string $fields): array
    {
        $methodCodes = [
            'store' => '$'.Str::snake($modelName).' = '.$modelName.'::create($request->validated());',
            'update' => '$'.Str::snake($modelName).'->update($request->validated());',
            'delete' => '',
        ];

        if ($fields != '') {
            $fieldsArray = explode(' ', $fields);

            foreach ($fieldsArray as $field) {
                [$name, $type] = explode(':', $field);
                $name = trim($name);

                if (in_array($name, config('crudstarter.image_fields'))) {
                    $methodCodes['store'] = $this->generate_image_store_code($modelName, $name);
                    $methodCodes['update'] = config('crudstarter.image_required') === true ?
                        $this->generate_required_image_update_code($modelName, $name) :
                        $this->generate_image_update_code($modelName, $name);
                    $methodCodes['delete'] = $this->generate_image_delete_code($modelName, $name);
                }
            }
        }

        return $methodCodes;
    }

    protected function generate_image_store_code(string $modelName, string $fieldName)
    {
        return '$'.Str::snake($modelName).' = '.$modelName.'::create($request->safe()->except(\''.$fieldName.'\'));'.PHP_EOL
            .'if ($request->hasFile(\''.$fieldName.'\')) {'.PHP_EOL
            .'    $'.Str::snake($modelName).'->storeImage(\''.$fieldName.'\', \''.Str::kebab($modelName).'-images\', $request->file(\''.$fieldName.'\'));'.PHP_EOL
            .'}';
    }

    protected function generate_image_update_code(string $modelName, string $fieldName)
    {
        $imageName = Str::snake($modelName);
        $folderName = Str::kebab($modelName);

        return '$data = $request->safe()->except(\''.$fieldName.'\');'.PHP_EOL
            .'if ((bool) $request->input(\''.$imageName.'_removed\') === true'.') {'.PHP_EOL
            .'$'.$imageName.'->deleteImage(\''.$fieldName.'\', \''.$folderName.'-images\');'.PHP_EOL
            .'$data[\''.$fieldName.'\'] = null;'.PHP_EOL
            .'}'.PHP_EOL.PHP_EOL
            .'$'.$imageName.'->update($data);'.PHP_EOL.PHP_EOL
            .'if ($request->hasFile(\''.$fieldName.'\')) {'.PHP_EOL
            .'    $'.Str::snake($modelName).'->updateImage(\''.$fieldName.'\', \''.$folderName.'-images\', $request->file(\''.$fieldName.'\'));'.PHP_EOL
            .'}';
    }

    protected function generate_required_image_update_code(string $modelName, string $fieldName)
    {
        return '$'.Str::lower($modelName).'->update($request->safe()->except(\''.$fieldName.'\'));'.PHP_EOL
            .'if ($request->hasFile(\''.$fieldName.'\')) {'.PHP_EOL
            .'    $'.Str::lower($modelName).'->updateImage(\''.$fieldName.'\', \''.Str::lower($modelName).'-images\', $request->file(\''.$fieldName.'\'));'.PHP_EOL
            .'}';
    }

    protected function generate_image_delete_code(string $modelName, string $fieldName)
    {
        return 'if($'.Str::snake($modelName).'->'.$fieldName.'){'.PHP_EOL
            .'$'.Str::snake($modelName).'->deleteImage(\''.$fieldName.'\', \''.Str::snake($modelName).'-images\');'.PHP_EOL
            .'}';
    }

    protected function resolve_model_should_have_file_trait(string $fields, string $modelName)
    {
        $defaultFile = config('crudstarter.default_image_type', 'url') === 'url' ? '"'.config('crudstarter.default_image_path').'"' : 'asset("'.config('crudstarter.default_image_path').'")';

        $fileTraitCodes = [
            'trait' => '',
            'traitImport' => '',
            'getImagePathAttribute' => '',
        ];
        if ($fields != '') {
            $fieldsArray = explode(' ', $fields);

            foreach ($fieldsArray as $field) {
                [$name, $type] = explode(':', $field);
                $name = trim($name);

                if (in_array($name, config('crudstarter.image_fields'))) {
                    $fileTraitCodes['trait'] = 'use UploadFileTrait;';
                    $fileTraitCodes['traitImport'] = 'use App\Traits\UploadFileTrait;';

                    $getImagePathAttribute = 'public function getImagePathAttribute():string {'.PHP_EOL;
                    $getImagePathAttribute .= "return \$this->".$name." ? asset('uploaded-images/".$modelName.'-images/\'.$this->'.$name.")".":".$defaultFile." ;".PHP_EOL;
                    $getImagePathAttribute .= "}".PHP_EOL;

                    $fileTraitCodes['getImagePathAttribute'] = $getImagePathAttribute;
                }
            }
        }
        return $fileTraitCodes;
    }


    protected function resolve_fields(string $fields): array
    {
        $fieldsArray = explode(' ', $fields);
        $data = [];

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

                $createTestFields .= "'".$item['name']."'".'=>'.$type.",".PHP_EOL.'            ';
            } elseif (isset($testFieldLookUp[$item['type']]) && !is_numeric($testFieldLookUp[$item['type']])) {

                $type = $testFieldLookUp[$item['type']];

                $createTestFields .= "'".$item['name']."'"."=>'".$type."',".PHP_EOL.'            ';
            } else {
                $createTestFields .= "'".$item['name']."'".'=>'."'enter value here',".PHP_EOL.'            ';
            }
        }
        return $createTestFields;
    }
}
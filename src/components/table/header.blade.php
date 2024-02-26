<thead class="bg-primary">
<tr class="text-uppercase">
    <th class="text-white" style="width:50px">SN</th>
    @foreach($headers as $key => $header)
        <th class="text-white" @if ($key !== 0) style="width:{{$key}}" @endif>{{$header}}</th>
    @endforeach
</tr>
</thead>
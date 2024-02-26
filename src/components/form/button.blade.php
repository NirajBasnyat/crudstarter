    @props([
      'id' => 'id',
      'class' =>'btn btn-sm btn-dark',
      'type' => 'submit'
    ])

    <br>
    <button id="{{$id}}" class="{{$class}}" type="{{$type}}">{{$slot}}</button>

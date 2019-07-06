<?php /*
{!! Form::open(['url' => 'recipe/submit']) !!}
    <h2>Add Recipe</h2>
    <br>
    <h4>Details</h4>
    <div class="form-row">
        <div class="col">{{Form::label('name', 'Name')}}</div>
        <div class="col-8">{{Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Recipe Name'])}}</div>
    </div>
    <div class="form-row">
        <div class="col">{{Form::label('prepTime', 'Preparation Time')}}</div>
        <div class="col-2">{{Form::text('prepTime', '', ['class' => 'form-control', 'placeholder' => '10'])}}</div>
        <div class="col-6">{{Form::select('prepTimeUnit', array('Seconds'=> 'Second(s)', 'Minutes'=> 'Minute(s)', 'Hours'=> 'Hour(s)'), null, ['class' => 'form-control'])}}</div>
    </div>
    <div class="form-row">
        <div class="col">{{Form::label('cookTime', 'Cook Time')}}</div>
        <div class="col-2">{{Form::text('cookTime', '', ['class' => 'form-control', 'placeholder' => '30'])}}</div>
        <div class="col-6">{{Form::select('cookTimeUnit', array('Seconds'=> 'Second(s)', 'Minutes'=> 'Minute(s)', 'Hours'=> 'Hour(s)'), null, ['class' => 'form-control'])}}</div>
    </div>
    <div class="form-row">
        <div class="col">{{Form::label('yield', 'Yield')}}</div>
        <div class="col-2">{{Form::text('yield', '', ['class' => 'form-control', 'placeholder' => '5'])}}</div>
        <div class="col-6">Servings</div>
    </div>
    <div class="form-row">
        <div class="col">{{Form::label('description', 'Description')}}</div>
        <div class="col-8">{{Form::textarea('description', '', ['class' => 'form-control', 'rows' => '3', 'placeholder' => 'Description'])}}</div>
    </div>
    <div class="form-row">
        <div class="col">{{Form::label('nutrition', 'Nutritional Details')}}</div>
        <div class="col-8">{{Form::textarea('nutrition', '', ['class' => 'form-control', 'rows' => '3', 'placeholder' => 'Nutritional Details'])}}</div>
    </div>
    <div class="form-row">
        <div class="col">{{Form::label('private', 'Private?')}}</div>
        <div class="col-8">{{Form::checkbox('private', 'true')}}</div>
    </div>
    <br>
    <div id="ingredients">
        <h4>Ingredients <button  type="button" id="addIngredient" class="fas fa-plus"></button></h4>
        {{ Form::hidden('ingredientCount', '1', ['id' => 'ingredientCount']) }}
        <div class="form-row">
            <div class="col-2">{{Form::text('quantity1', '', ['class' => 'form-control', 'placeholder' => '10'])}}</div>
            <div class="col-4">{{Form::text('unit1', '', ['class' => 'form-control unit', 'placeholder' => 'Unit'])}}</div>
            <div class="col-6">{{Form::text('itemName1', '', ['class' => 'form-control', 'placeholder' => 'Ingedient Name'])}}</div>
        </div>
    </div>
    <br>
    <div id="steps">
        <h4>Steps <button  type="button" id="addStep" class="fas fa-plus"></button></h4>
        {{ Form::hidden('stepCount', '1', ['id'=> 'stepCount']) }}
        <div class="form-row">
                <div class="col-12">{{Form::textarea('step1', '', ['class' => 'form-control autogrow', 'min-rows' => '1', 'max-rows' => '5', 'rows' => '1', 'placeholder' => 'Step Details'])}}</div>
        </div>
    </div>
    <br>
    <div>
        {{Form::submit('Submit', ['type' => 'submit', 'class' => 'btn btn-primary float-right'])}}
    </div>
    <br>
{!! Form::close() !!}
*/
?>

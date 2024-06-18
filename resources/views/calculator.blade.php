<!-- formulario de calculacion de sueldo liquido a partir de variables de ingresos chilenas -->
<!-- enviar el form al controlador CalculatorController, metodo calculate -->
<!DOCTYPE html>
<html>
<head>
    <title>Calculadora de sueldo líquido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container">
        <h1>Calculadora de sueldo líquido</h1>
        <p>Ingrese los siguientes datos para calcular su sueldo líquido:</p>
        <form action="" method="POST" id="form">
            @csrf
            <div class="grid gap-3">
                <div class="form-group row p-3 border">
                    <div class="col-6">
                        <label for="baseIncome">Sueldo base ( * )</label>
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control" id="baseIncome" name="baseIncome" required>
                    </div>
                </div>
                <div class="form-group row p-3 border">
                    <div class="col-6">
                        <label for="gratification">Gratificación ( * )</label>
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control" id="gratification" name="gratification" required>
                    </div>
                </div>
                <div class="form-group row p-3 border">
                    <div class="col-6">
                        <label for="afp">Institución de AFP</label>
                    </div>
                    <div class="col-6">
                        <select class="form-select" name="afp" id="afp">
                            <option value="AFP Capital">AFP Capital</option>
                            <option value="AFP Cuprum">AFP Cuprum</option>
                            <option value="AFP Habitat">AFP Habitat</option>
                            <option value="AFP Modelo">AFP Modelo</option>
                            <option value="AFP Planvital">AFP Planvital</option>
                            <option value="AFP Provida">AFP Provida</option>
                            <option value="AFP Uno">AFP Uno</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row p-3 border">
                    <div class="col-6">
                        <label for="healthPercent">Porcentaje de cotización</label>
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control" id="healthPercent" name="healthPercent" readonly="readonly" value="7">
                    </div>
                </div>
                <div class="form-group row p-3 border">
                    <div class="col-6">
                        <label for="health">Sistema de salud</label>
                    </div>
                    <div class="col-6">
                        <select class="form-select" name="health" id="health">
                            @foreach ($isapres as $isapre)
                                <option value="{{ $isapre }}">{{ $isapre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row p-3 border">
                    <div class="col-6">
                        <label for="travelBonus">Locomoción</label>
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control" id="travelBonus" name="travelBonus" value="36000">
                    </div>
                </div>
                <div class="form-group row p-3 border" id="foodBonusFormGroup">
                    <div class="col-6">
                        <label for="foodBonus">Colación</label>
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control" id="foodBonus" name="foodBonus" value="52000">
                    </div>
                </div>
                <div class="row p-3">
                    <button type="submit" class="btn btn-primary">Calcular</button>
                    <h3 id="netIncomeH3" name="netIncomeH3"></h3>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    $("form").submit(function(e) {
                        e.preventDefault();
                        if (!validateForm()) {
                            return;
                        }
                        $.ajax({
                            type: 'POST',
                            url: '/calculate',
                            data: $(this).serialize(),
                            success: function(response) {
                                $("#netIncomeH3").text('Su sueldo líquido es ' + response['netIncome'] + ' CLP');
                                $("#netIncomeH3").show();
                            }
                        });
                    });
                    function validateForm(){
                        var baseIncome = $('#baseIncome').val();
                        var gratification = $('#gratification').val();
                        if (baseIncome === '' || gratification === '') {
                            alert('Los campos marcados con (*) son obligatorios');
                            return false;
                        }
                        var healthPercent = $('#healthPercent').val();
                        if (healthPercent === '') {
                            alert('Debes indicar el porcentaje de cotización de salud');
                            return false;
                        }
                        return true;
                    }
                    function validateFoodBonus(){
                        // if gross income > 1200000, food bonus must be 0, and hidden
                        var baseIncome = $('#baseIncome').val();
                        var gratification = $('#gratification').val();
                        var foodBonus = $('#foodBonus').val();
                        if (baseIncome + gratification > 1200000) {
                            $('#foodBonus').val('0');
                            $('#foodBonusFormGroup').hide();
                        }else{
                            $('#foodBonusFormGroup').show();
                        }
                    }
                    $('#baseIncome').on('change', function() {
                        validateFoodBonus();
                    });
                    $('#healthPercent').on('change', function() {
                        var health = $('#healthPercent').val();
                        if (health < 0 || health > 100) {
                            alert('El porcentaje de cotización de salud debe ser un número entre 0 y 100');
                            $('#healthPercent').val('');
                        }
                    });
                    $('#baseIncome').on('change', function() {
                        var baseIncome = $('#baseIncome').val();
                        if (baseIncome < 0) {
                            alert('El sueldo base no puede ser negativo');
                            $('#baseIncome').val('');
                        }
                    });
                    $('#gratification').on('change', function() {
                        var gratification = $('#gratification').val();
                        if (gratification < 0) {
                            alert('La gratificación no puede ser negativa');
                            $('#gratification').val('');
                        }
                    });
                    $('#health').on('change', function() {
                        if($(this).val() !== 'FONASA') {
                            $("#healthPercent").prop('readonly', false);
                            $("#healthPercent").val('');
                        }
                        else {
                            $("#healthPercent").prop('readonly', true);
                            $("#healthPercent").val('7');
                        }
                    });
                    if ($('#health').val() === 'FONASA') {
                        $("#healthPercent").prop('readonly', true);
                        $("#healthPercent").val('7');
                    }else{
                        $("#healthPercent").prop('readonly', false);
                        $("#healthPercent").val('');
                    }
                });
            </script>
        </form>
    </div>
</html>

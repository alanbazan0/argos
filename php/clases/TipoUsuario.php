<?php
// Niveles de usuario de apps-handel (mismos valores que en saha, sivah y handel_api).
abstract class TipoUsuario
{
    const ADMINISTRADOR = 1;
    const COORDINADOR = 2;
    const INSPECTOR = 3;
    const USUARIO = 4;
    const SUPERVISOR = 5;
    const CAPACITADO = 6;
}

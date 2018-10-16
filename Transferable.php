<?php

interface Transferable
{
    public function send($msg);
    public function medium();
}
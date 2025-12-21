<?php
namespace liansu\interfaces;

interface ISortableRun extends IRun
{
    public function getSort(): int;
}
<?php

namespace App\Services\System;

use Illuminate\Support\Facades\DB;

class CodeGeneratorService
{
    /**
     * Generate the next sequential code.
     */
    public function generate(
        string $table,
        string $column,
        string $prefix,
        int $digits = 6
    ): string {

        $year = now()->format('Y');

        $latest = DB::table($table)
            ->where($column, 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->value($column);

        $next = 1;

        if ($latest) {

            $parts = explode('-', $latest);

            $next = ((int) end($parts)) + 1;
        }

        return sprintf(
            '%s-%s-%0' . $digits . 'd',
            $prefix,
            $year,
            $next
        );
    }
}

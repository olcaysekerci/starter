<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait TransactionTrait
{
    /**
     * Transaction içinde işlem yap
     */
    protected function executeInTransaction(callable $callback, string $operation = 'operation')
    {
        try {
            DB::beginTransaction();
            
            $result = $callback();
            
            DB::commit();
            
            Log::info("{$operation} başarıyla tamamlandı", [
                'operation' => $operation,
                'user_id' => auth()->id()
            ]);
            
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("{$operation} sırasında hata oluştu", [
                'operation' => $operation,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            throw $e;
        }
    }

    /**
     * Transaction içinde boolean işlem yap
     */
    protected function executeBooleanInTransaction(callable $callback, string $operation = 'operation'): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $callback();
            
            DB::commit();
            
            if ($result) {
                Log::info("{$operation} başarıyla tamamlandı", [
                    'operation' => $operation,
                    'user_id' => auth()->id()
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("{$operation} sırasında hata oluştu", [
                'operation' => $operation,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            throw $e;
        }
    }

    /**
     * Transaction içinde model oluştur
     */
    protected function createInTransaction(callable $callback, string $operation = 'model creation')
    {
        return $this->executeInTransaction($callback, $operation);
    }

    /**
     * Transaction içinde model güncelle
     */
    protected function updateInTransaction(callable $callback, string $operation = 'model update'): bool
    {
        return $this->executeBooleanInTransaction($callback, $operation);
    }

    /**
     * Transaction içinde model sil
     */
    protected function deleteInTransaction(callable $callback, string $operation = 'model deletion'): bool
    {
        return $this->executeBooleanInTransaction($callback, $operation);
    }
} 
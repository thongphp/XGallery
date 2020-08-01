<?php

namespace Tests\Traits;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\SQLiteBuilder;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Fluent;

trait FixSQLite
{
    /**
     * @SuppressWarnings("unused")
     *
     * @return void
     */
    public function hotfixSqlite(): void
    {
        Connection::resolverFor('sqlite', function ($connection, $database, $prefix, $config) {
            return new class($connection, $database, $prefix, $config) extends SQLiteConnection {
                public function getSchemaBuilder()
                {
                    if ($this->schemaGrammar === null) {
                        $this->useDefaultSchemaGrammar();
                    }

                    return new class($this) extends SQLiteBuilder {
                        protected function createBlueprint($table, \Closure $callback = null)
                        {
                            return new class($table, $callback) extends Blueprint
                            {
                                public function dropForeign($index): Fluent
                                {
                                    return new Fluent();
                                }
                            };
                        }
                    };
                }
            };
        });
    }
}

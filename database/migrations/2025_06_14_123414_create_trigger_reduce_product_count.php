<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<SQL
            CREATE OR REPLACE FUNCTION reduce_product_count()
            RETURNS TRIGGER AS \$\$
            BEGIN
                UPDATE products
                SET count = count - NEW.qty
                WHERE id = NEW.product_id;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        SQL);

        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_reduce_count_after_sale
            AFTER INSERT ON sale_items
            FOR EACH ROW
            EXECUTE FUNCTION reduce_product_count();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_reduce_count_after_sale ON sale_items;");
        DB::unprepared("DROP FUNCTION IF EXISTS reduce_product_count();");
    }
};

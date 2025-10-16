#!/bin/bash

APP_CONTAINER=laravel_app
DB_CONTAINER=mysql_db
DB_NAME=duxone
DB_USER=duxuser
DB_PASS=secret

echo "🔄 Step 1: Refreshing migrations + running AccountingSeeder..."
docker exec -it $APP_CONTAINER bash -c "php artisan migrate:fresh --seed --seeder=AccountingSeeder" >/dev/null 2>&1

if [ $? -eq 0 ]; then
  echo "✅ Migrations + Seeder executed successfully"
else
  echo "❌ Migrations/Seeder failed"
  exit 1
fi

echo "🔍 Step 2: Checking tables existence..."
TABLES=("clients" "invoices" "invoice_items" "payments" "journal_entries" "journal_lines")
for TABLE in "${TABLES[@]}"; do
  RESULT=$(docker exec -i $DB_CONTAINER mysql -u$DB_USER -p$DB_PASS $DB_NAME -e "SHOW TABLES LIKE '$TABLE';" | grep $TABLE)
  if [ "$RESULT" == "$TABLE" ]; then
    echo "✅ Table $TABLE exists"
  else
    echo "❌ Table $TABLE missing"
  fi
done

echo "📊 Step 3: Checking sample data..."
COUNT_CLIENTS=$(docker exec -i $DB_CONTAINER mysql -u$DB_USER -p$DB_PASS $DB_NAME -N -e "SELECT COUNT(*) FROM clients;")
COUNT_INVOICES=$(docker exec -i $DB_CONTAINER mysql -u$DB_USER -p$DB_PASS $DB_NAME -N -e "SELECT COUNT(*) FROM invoices;")

if [ "$COUNT_CLIENTS" -gt 0 ]; then
  echo "✅ Clients seeded ($COUNT_CLIENTS)"
else
  echo "❌ No clients found"
fi

if [ "$COUNT_INVOICES" -ge 10 ]; then
  echo "✅ Invoices seeded ($COUNT_INVOICES)"
else
  echo "❌ Invoices not seeded correctly ($COUNT_INVOICES)"
fi

echo "💰 Step 4: Checking VAT column integrity..."
VAT_EXISTS=$(docker exec -i $DB_CONTAINER mysql -u$DB_USER -p$DB_PASS $DB_NAME -e "SHOW COLUMNS FROM invoices LIKE 'vat_amount';" | grep vat_amount)
if [ -n "$VAT_EXISTS" ]; then
  echo "✅ invoices.vat_amount column exists"
else
  echo "❌ invoices.vat_amount column missing"
fi

echo "🔗 Step 5: Checking relations..."
RELATION_CHECK=$(docker exec -i $DB_CONTAINER mysql -u$DB_USER -p$DB_PASS $DB_NAME -N -e "SELECT COUNT(*) FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoices LIMIT 1);" )
if [ "$RELATION_CHECK" -gt 0 ]; then
  echo "✅ invoice_items linked to invoices"
else
  echo "❌ No linked invoice_items found"
fi

echo "🎉 Verification finished!"

Product Module Documentation
1. Migration Details

The `products` table includes the following columns:
- id (Primary Key)
- ClientName (string)
- ProductName (string)
- ProductPrice (string)
- Store (integer)
- Status (enum: Yes, No, default: Yes)
- AddedBy (integer, nullable)
- UpdatedBy (integer, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable - for soft deletes)

2. Model: ProductMaster

Model: `ProductMaster`
- Uses: SoftDeletes, HasFactory
- Table: products
- Fillable fields: ClientName, ProductName, ProductPrice, Store, Status, AddedBy, UpdatedBy, created_at, updated_at

3. Controller: ProductMasterController
3.1 index(Request $request)

Fetches and filters product records using optional filters:
- Id (integer)
- Name (string, filters ClientName)
- Status (enum: Yes/No)

Utilizes Redis caching for performance improvement.

3.2 store(Request $request)

Adds a new product record. Validates required fields:
- ClientName (required, unique)
- ProductName (required)
- Status (required: Yes/No)
- ProductPrice (optional)
- Store (optional)
- AddedBy (optional)

Flushes Redis cache after storing.

3.3 update(Request $request)

Updates an existing product record based on ID. Validates:
- ClientName (required)
- ProductName (required)
- Status (required)
- ProductPrice (optional)
- Store (optional)
- UpdatedBy (optional)

Flushes Redis cache after update.

3.4 destroy(Request $request)

Soft deletes a product based on ID using Eloquent's soft delete.
Flushes Redis cache after deletion.

4. API Payload Examples
4.1 Store Request Payload (POST /products)

{
  "ClientName": "Client A",
  "ProductName": "Product X",
  "ProductPrice": "99.99",
  "Store": 10,
  "Status": "Yes",
  "AddedBy": 1
}

4.2 Update Request Payload (PUT /products)

{
  "id": 1,
  "ClientName": "Client A",
  "ProductName": "Product Y",
  "ProductPrice": "89.99",
  "Store": 12,
  "Status": "No",
  "UpdatedBy": 2
}

4.3 Delete Request Payload (DELETE /products)

{
  "id": 1
}

5. API Routes

- GET /products
  - Fetch list of products. Optional filters: Id, Name, Status

- POST /products
  - Create new product

- PUT /products
  - Update existing product

- DELETE /products
  - Soft delete product by ID


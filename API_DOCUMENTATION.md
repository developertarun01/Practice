# Servon API Documentation

## Authentication

All endpoints require a valid user session. Session is established via `/api/login.php`. Role-based access control is enforced on all endpoints.

## Response Format

All API endpoints return JSON responses with the following structure:

```json
{
    "success": true/false,
    "message": "Description of result",
    "data": {
        // Response data
    }
}
```

## Endpoints

### Leads API

#### Get Lead Details

- **URL:** `/api/get-lead.php`
- **Method:** GET
- **Parameters:** `id` (Lead ID)
- **Response:** Lead details including all comments
- **Access:** Admin, Sales, Allocation, Support

#### Update Lead Status

- **URL:** `/api/update-lead-status.php`
- **Method:** POST
- **Parameters:**
  - `lead_id` (required)
  - `status` (required) - Fresh|In progress|Converted|Dropped
- **Response:** Success message
- **Access:** Admin, Sales, Allocation

#### Add Comment to Lead

- **URL:** `/api/add-comment.php`
- **Method:** POST
- **Parameters:**
  - `lead_id` (required)
  - `comment` (required) - Comment text
- **Response:** Comment ID
- **Access:** Admin, Sales, Allocation, Support

### Bookings API

#### Create Booking

- **URL:** `/api/create-booking.php`
- **Method:** POST
- **Parameters:**
  - `lead_id` (optional)
  - `customer_name` (required)
  - `customer_email` (optional)
  - `customer_phone` (required) - 10 digits
  - `service` (required)
  - `full_address` (optional)
  - `starts_at` (optional) - DateTime format
  - `job_hours` (optional) - Number
  - `salary_bracket` (optional) - String
- **Response:** Booking ID
- **Access:** Admin, Sales, Allocation

#### Get Booking Details

- **URL:** `/api/get-booking.php`
- **Method:** GET
- **Parameters:** `id` (Booking ID)
- **Response:** Complete booking details
- **Access:** Admin, Sales, Allocation

#### Update Booking

- **URL:** `/api/update-booking.php`
- **Method:** POST
- **Parameters:**
  - `booking_id` (required)
  - `customer_name` (optional)
  - `customer_email` (optional)
  - `customer_phone` (optional)
  - `full_address` (optional)
  - `status` (optional)
  - `starts_at` (optional)
  - `job_hours` (optional)
  - `salary_bracket` (optional)
- **Response:** Success message
- **Access:** Admin, Sales, Allocation

### Payments API

#### Create Payment

- **URL:** `/api/create-payment.php`
- **Method:** POST
- **Parameters:**
  - `booking_id` (optional)
  - `customer_name` (required)
  - `customer_email` (required)
  - `customer_phone` (required) - 10 digits
  - `service` (required)
  - `purpose` (required)
  - `total_amount` (required) - Float > 0
  - `payment_method` (optional)
- **Response:** Payment ID
- **Access:** Admin, Sales, Support

#### Get Payment Details

- **URL:** `/api/get-payment.php`
- **Method:** GET
- **Parameters:** `id` (Payment ID)
- **Response:** Complete payment details
- **Access:** Admin, Sales, Support

#### Update Payment Status

- **URL:** `/api/update-payment-status.php`
- **Method:** POST
- **Parameters:**
  - `payment_id` (required)
  - `status` (required) - Pending|Completed|Failed|Refunded
- **Response:** Success message
- **Access:** Admin, Sales, Support

### Professionals API

#### Create Professional

- **URL:** `/api/create-professional.php`
- **Method:** POST
- **Parameters:**
  - `name` (required)
  - `phone` (required) - 10 digits, unique
  - `email` (optional)
  - `service` (required)
  - `gender` (required) - Male|Female|Other
  - `experience` (optional) - Years
  - `location` (optional)
  - `status` (optional) - Default: Active
- **Response:** Professional ID
- **Access:** Admin, Allocation

#### Get Professional Details

- **URL:** `/api/get-professional.php`
- **Method:** GET
- **Parameters:** `id` (Professional ID)
- **Response:** Complete professional details
- **Access:** Admin, Allocation

#### Update Professional

- **URL:** `/api/update-professional.php`
- **Method:** POST
- **Parameters:**
  - `professional_id` (required)
  - `name` (optional)
  - `email` (optional)
  - `phone` (optional)
  - `experience` (optional)
  - `status` (optional)
  - `verify_status` (optional) - Verified|Pending|Rejected
  - `rating` (optional) - 0-5
- **Response:** Success message
- **Access:** Admin, Allocation

### Users API

#### Create User

- **URL:** `/api/create-user.php`
- **Method:** POST
- **Parameters:**
  - `name` (required)
  - `email` (required, unique)
  - `phone` (required) - 10 digits
  - `password` (required) - Min 6 characters
  - `role` (required) - Admin|Sales|Allocation|Support
- **Response:** User ID
- **Access:** Admin only

#### Get User Details

- **URL:** `/api/get-user.php`
- **Method:** GET
- **Parameters:** `id` (User ID)
- **Response:** User details (password excluded)
- **Access:** Admin only

#### Update User

- **URL:** `/api/update-user.php`
- **Method:** POST
- **Parameters:**
  - `user_id` (required)
  - `name` (optional)
  - `email` (optional)
  - `phone` (optional)
  - `role` (optional)
  - `enabled` (optional) - 0 or 1
  - `password` (optional) - Min 6 characters
- **Response:** Success message
- **Access:** Admin only

### Follow-ups API

#### Create Follow-up

- **URL:** `/api/create-follow-up.php`
- **Method:** POST
- **Parameters:**
  - `lead_id` (required)
  - `direction` (required) - Inbound|Outbound
  - `channel` (required) - Phone|Email|WhatsApp|SMS
  - `comments` (optional)
  - `reminder_at` (optional) - DateTime format
- **Response:** Follow-up ID
- **Access:** Admin, Sales, Allocation, Support

### Phone Calls API

#### Create Phone Call

- **URL:** `/api/create-phone-call.php`
- **Method:** POST
- **Parameters:**
  - `customer_number` (required) - 10 digits
  - `direction` (required) - Inbound|Outbound
  - `duration` (optional) - Seconds
  - `agent_id` (optional) - User ID
  - `tag` (optional) - String
- **Response:** Call ID
- **Access:** Admin, Sales

### Service Requests API

#### Create Service Request

- **URL:** `/api/create-service-request.php`
- **Method:** POST
- **Parameters:**
  - `booking_id` (required)
  - `professional_id` (required)
  - `remarks` (optional)
- **Response:** Service Request ID
- **Access:** Admin, Support

## Error Codes & Messages

### Common Errors

- `"Invalid request method"` - Wrong HTTP method used
- `"Validation failed"` - One or more parameters invalid
- `"[Entity] not found"` - Requested record doesn't exist
- `"[Field] already exists"` - Duplicate unique field
- `"Error [operation] [entity]"` - Database error occurred
- `"Unauthorized access"` - User role insufficient

### Validation Rules

- **Phone Numbers:** Must be exactly 10 digits
- **Emails:** Must be valid email format
- **Passwords:** Minimum 6 characters
- **Amounts:** Must be greater than 0
- **Dates:** Must be valid DateTime format

## Rate Limiting

No rate limiting is currently implemented. Implement if needed for production.

## Security Notes

- All inputs are validated and escaped
- Passwords are hashed using SHA256
- SQL injection prevention through escaping
- Role-based access control enforced
- CSRF protection via session validation
- HTTPS recommended for production

## Examples

### Create a New Booking

```bash
curl -X POST http://localhost/servon/api/create-booking.php \
  -d "customer_name=John Doe" \
  -d "customer_phone=9876543210" \
  -d "service=House Maid" \
  -d "full_address=123 Main St" \
  -d "job_hours=4" \
  -d "salary_bracket=10000-12000"
```

### Create a Payment

```bash
curl -X POST http://localhost/servon/api/create-payment.php \
  -d "customer_name=John Doe" \
  -d "customer_email=john@example.com" \
  -d "customer_phone=9876543210" \
  -d "service=House Maid" \
  -d "purpose=Monthly Payment" \
  -d "total_amount=10000" \
  -d "payment_method=UPI"
```

### Get Lead Details

```bash
curl -X GET "http://localhost/servon/api/get-lead.php?id=1"
```

### Update Lead Status

```bash
curl -X POST http://localhost/servon/api/update-lead-status.php \
  -d "lead_id=1" \
  -d "status=Converted"
```

### Add Comment

```bash
curl -X POST http://localhost/servon/api/add-comment.php \
  -d "lead_id=1" \
  -d "comment=Customer seems interested in 3-day trial"
```

## Database Relationships

### Lead → Comment

- One lead has many comments
- Comments cannot exist without a lead
- Deleting a lead should cascade delete comments

### Lead → Booking

- One lead can have multiple bookings
- Booking optional linked to lead

### Booking → Payment

- One booking can have multiple payments
- Payment optional linked to booking

### Booking → Service Request

- One booking can have multiple service requests
- Links professional to booking

### Service Request → Professional

- Many service requests can use same professional
- Links professional to specific service request

## Future Enhancements

- Implement API rate limiting
- Add request/response logging
- Implement caching for frequently accessed data
- Add pagination for list endpoints
- Implement soft deletes
- Add audit trail for sensitive operations
- Implement API versioning

CREATE TABLE customers (
  id INT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100),
  country VARCHAR(50),
  signup_date DATE
);

INSERT INTO customers VALUES
(1, 'Alice Smith', 'alice@example.com', 'USA', '2023-01-10'),
(2, 'Bob Johnson', 'bob@example.com', 'Canada', '2023-01-15'),
(3, 'Carol Jones', 'carol@example.com', 'Australia', '2023-01-20');

CREATE TABLE products (
  id INT PRIMARY KEY,
  name VARCHAR(100),
  category VARCHAR(50),
  price DECIMAL(10, 2)
);

INSERT INTO products VALUES
(1, 'Wireless Mouse', 'Electronics', 25.00),
(2, 'Office Chair', 'Furniture', 120.00),
(3, 'Water Bottle', 'Home', 15.00);

CREATE TABLE orders (
  id INT PRIMARY KEY,
  customer_id INT,
  product_id INT,
  quantity INT,
  order_date DATE,
  FOREIGN KEY (customer_id) REFERENCES customers(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO orders VALUES
(1, 1, 1, 2, '2023-02-01'),
(2, 2, 2, 1, '2023-02-05'),
(3, 1, 3, 5, '2023-02-10'),
(4, 3, 1, 1, '2023-02-15');

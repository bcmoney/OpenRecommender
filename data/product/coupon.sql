-- coupon --
CREATE TABLE coupon (coupon_id NOT NULL AUTONUM AS PRIMARY KEY, coupon_title VARCHAR(100), coupon_link VARCHAR(200), coupon_image VARCHAR(200), coupon_description VARCHAR(1000), coupon_code VARCHAR(50));

-- SOURCES --
Groupon
Coupons
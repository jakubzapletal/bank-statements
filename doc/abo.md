# ABO

The ABO format is commonly used for exchanging financial messages in the Czech Republic and Slovakia. The format
structure is determinedly defined, according to the below-stated overview. Record description is not used during the
export of the ABO format.


### Account Statement

Sequence No. | Name | F/V | Minimum Length | Maximum Length | Content | Comment
-------------|------|-----|----------------|----------------|---------|--------
1  | Type of record              | F | 3  | 074                  |
2  | Client account number       | F | 16 | NNNNNNNNNNNNNNNN     | 1
3  | Abbreviated client name     | F | 20 | AAAAAAAAAAAAAAAAAAAA |
4  | Old balance name            | F | 6  | ddmmyy               |
5  | Old balance                 | F | 14 | NNNNNNNNNNNNNN       | 5
6  | Old balance sign            | F | 1  | (plus) or (minus)    | 2
7  | New balance                 | F | 14 | NNNNNNNNNNNNNN       | 5
8  | New balance sign            | F | 1  | (plus) or (minus)    | 2
9  | Transactions – debit        | F | 14 | NNNNNNNNNNNNNN       | 5
10 | Sign of debit transactions  | F | 1  | (plus) or (minus)    | 3
11 | Transactions – credit       | F | 14 | NNNNNNNNNNNNNN       | 5
12 | Sign of credit transactions | F | 1  | (plus) or (minus)    | 3
13 | Statement sequence number   | F | 3  | NNN                  |
14 | Posting date                | F | 6  | ddmmyy               |
15 | Filler                      | F | 14 | (space)              | 4
16 | End-of-record character     | F | 2  | CR LF                |

**Comments:**
1.  Depending on the application settings the data can be stated in the so-called internal format – see the
internal format description below.
2.  “Plus” sign in case of positive balance and the “minus” sign in case of negative balance.
3.  Character “zero” or the “minus” sign if the reversal prevails.
4.  14 “space” characters due to the unification of length for record types 074 and 075.
5.  Amount is stated in hellers (the last two characters).


### Transaction item

Sequence No. | Name | F/V | Minimum Length | Maximum Length | Content | Comment
-------------|------|-----|----------------|----------------|---------|--------
1  | Type of record          | F  | 3  | 075
2  | Client account number   | F  | 16 | NNNNNNNNNNNNNNNN     | 1
3  | Counter-account number  | F  | 16 | NNNNNNNNNNNNNNNN     | 1,2
4  | Document number         | F  | 13 | AAAAAAAAAAAAA        | 3
5  | Amount                  | F  | 12 | NNNNNNNNNNNN         | 10
6  | Posting code            | F  | 1  | N                    | 4
7  | V-symbol                | F  | 10 | NNNNNNNNNN           |
8  | K-symbol.               | F  | 10 | NNNNNNNNNN           | 5
9  | S-symbol                | F  | 10 | NNNNNNNNNN           |
10 | Value                   | F  | 6  | ddmmyy               | 6
11 | Additional detail       | F  | 20 | AAAAAAAAAAAAAAAAAAAA | 7
12 | Change of item code     | F  | 1  | A                    | 8
13 | Type of data            | F  | 4  | rmoo                 | 9
14 | Due date                | F  | 6  | ddmmyy               |
15 | End-of-record character | F  | 2  | CR LF                |

**Comments:**
1.  Depending on the application settings the data can be stated in the so-called internal format – see the
internal format description below.
2.  Account number of the partner organisation.
3.  Item identification number – document number supplemented with leading zeros.
4.  The detail is related to the detail with Sequence No. 2 and its content is specified as follows: “1” – debit
item, “2” – credit item, “3” – debit entry reversal, “4” – credit item reversal.
5.  Constant symbol as such is usually stated on the 1st to the 4th position from the right, bank code of the
bank that maintains account presented in the Field No. 3 is stated on the 5th to the 8th position from the
right.
6.  Date, from which the item is included into the balance for interest calculation.
7.  Abbreviated name of partner organisation, or type of transaction. Text field is aligned to the left and
supplemented with blank spaces up to the field length.
8.  Information on additional item change in the payment index-file and/or its partial payment: “0” – item was
not additionally changed or partially paid, “Z” – item was changed, “C” – partial payment, “P” – item was
additionally changed and partially paid.
9.  The detail acquires different values depending on the input data character from the payer point of view.
Values important for customers: r – always “1”, m – for operations in CZK “1”, oo – odd number (“01”) –
payment, even number (“02”) – collection/deposit.
10.  Amount is stated in hellers (the last two characters).

# ABO

The ABO format is commonly used for exchanging financial messages in the Czech Republic and Slovakia. The format
structure is determinedly defined, according to the below-stated overview. Record description is not used during the
export of the ABO format.


## Account Statement

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
1 - Depending on the application settings the data can be stated in the so-called internal format – see the
internal format description below.
2 - “Plus” sign in case of positive balance and the “minus” sign in case of negative balance.
3 - Character “zero” or the “minus” sign if the reversal prevails.
4 - 14 “space” characters due to the unification of length for record types 074 and 075.
5 - Amount is stated in hellers (the last two characters).


## Transaction item

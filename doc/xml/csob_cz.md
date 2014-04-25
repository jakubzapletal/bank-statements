# XML - ČSOB (CZ)

All files are in charset **WINDOWS-1250** and keep validity of standard XML format.

## Legend of shortcuts

Following tables includes shortcuts ant there are their description:

**Type** = type of record

* C = Char
* D = Date in format DD.MM.RRRR
* T = Time
* N = Num

**Max L** = max. length of record - in the case of amount (type = N) including numbers after point

**M / O** = valency

* M = Mandatory
* O = Optional

## Structure of tags

### Root tag

Root tag is defined by tag `<FINSTA>` and `</FINSTA>`.

### Head of statement

Head begins by ``<FINSTA03>` and ends by `</FINSTA03>`.

Head contains following tags:

Tag | Type | Max L | M/O | Description
----|------|-------|-----|------------
S28_CISLO_VYPISU | N | 5    | M | Order ID
S25_CISLO_UCTU   | C | 34   | M | Account number of client
SHORTNAME        | C | 35   | O | Short name of client
S60_CD_INDIK     | C | 1    | M | Post code, C - positive, D - negative
S60_DATUM        | D | 10   | M | Date of last statement
S60_MENA         | C | 3    | M | ISO code of account currency
S60_CASTKA       | N | 17.2 | M | Last balance
SUMA_KREDIT      | N | 17.2 | O | Credit turnover
SUMA_DEBIT       | N | 17.2 | O | Debit turnover
S62_CD_INDIK     | C | 1    | M | C/D indication of balance
S62_DATUM        | D | 10   | M | Date of balance
S62_CASTKA       | N | 17.2 | M | Amount of balance
S64_CD_INDIK     | C | 1    | O | C/D indication of available balance
S64_DATUM        | D | 10   | O | Date of available balance
S64_CASTKA       | N | 17.2 | O | Amount of available balance
FREKVENCE        | C | 1    | M | Frequency of statement, codes: D - daily, W, M, Q, H, Y
STAT_AST         | C | 1    | M | Status of statement: 9 - original, 7 - duplicate
TYPE_ACC_TXT     | C | 35   | M | Type of account
IR_START         | N | 9.3  | O | Origin interest rate
IR_END           | N | 9.3  | O | New interest rate
FREKV_TXT        | C | 20   | O | Frequency of statement by text


### Transactions

Every transaction has own pair tag `<FINSTA05>` which is situated in statement tag `<FINSTA03>` according other info tags of statement.

Transaction contains following tags:

Tag | Type | Max L | M/O | Description
----|------|-------|-----|------------
S28_POR_CISLO  | N | 6    | M | Order
REF_TRANS_SYS  | C | 17   | O | Reference number of bank
TYP_CC         | N | 2    | O | Type of message by Clearing center
FACAERQ        | C | 35   | O | ID of origin oder
OPDIRC         | C | 3    | O | Operative code: DDT - collection, TRF - payment
S61_DATUM      | D | 10   | O | Date of foreign currency
S61_DINPUT     | D | 10   | O | Date of oder
DPROCD         | D | 10   | O | Date of process
DPROCOTHER     | D | 10   | O | Date of outcome
S61_CD_INDIK   | C | 2    | M | Sign of transaction
S61_MENA       | C | 3    | O | ISO code of currency
S61_CASTKA     | N | 17.2 | M | Amount
S61_TRANSAKCE  | C | 4    | O | Type of transaction
S61_REFERENCE  | C | 16   | O | Transaction reference
S61_POST_ORIG  | C | 8    | O | Code of description
S61_POST_NAR   | C | 30   | O | Description of article
DOM_ZAHR       | C | 3    | M | Distinction of trans.
S86_SPECSYMOUR | C | 10   | O | Specific symbol
S86_VARSYMOUR  | C | 10   | O | Variable symbol
S86_KONSTSYM   | C | 10   | O | Constant symbol
PART_BANK_ID   | C | 35   | O | Code of bank counter account
PART_ACCNO     | C | 35   | O | Account number
PART_ACC_ID    | C | 35   | O | Name of counter-account
S86_SPECSYMPAR | C | 10   | O | Specific symbol of counter-side
S86_VARSYMPAR  | C | 10   | O | Variable symbol of counter-side
PART_ID1_1     | C | 70   | O | Info for receiver
PART_ID1_2     | C | 70   | O | Info for receiver
PART_ID2_1     | C | 70   | O | Info for receiver
PART_ID2_2     | C | 70   | O | Info for receiver
PART_MSG_1     | C | 70   | O | Client message
PART_MSG_2     | C | 70   | O | Client message
ORIG_AMOUNT    | N | 17.2 | O | Amount
RATE           | N | 10.6 | O | Rate
ORIG_CURR      | C | 3    | O | ISO code of transaction currency
ACC_BAL        | N | 17.2 | O | Balance after move
S62M_CD_INDIK  | C | 1    | O | C/D indication balance after move
GRP_TR         | N | 3    | O | Code of transaction group
REMARK         | C | 35   | O | Note
S64_ BALAVL    | N | 16.2 | O | Available balance
S64_CD_IND     | C | 1    | O | C/D indication of balance
S64_ TIME      | T | 6    | O | Time of balance
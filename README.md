"# rentallaravel" 

The original purpose of this repository is to get acquainted with Laravel and implement the following standards features:

- Database connection and scaffolding from database first (OK)
- Authentication (OK)
- Authorizarion with some simple roles (OK)
- Impersonation (OK)
- Menu system (OK)
- Pagination (OK)
- Sort (OK)
- Search
- Server validation of data saved (after Mutators)  (OK)
- Internationalization:
  o International decimal and date handling  (OK)
  o Validation should also work here (OK)
  o Translation of user interface (OK)
  o Translation of database stored data
- Handling of Session (OK)
- Handling of REST with security token (not tested)
- Unit testing (OK)
- Interface testing (not tested)
- Compare performance with similar systems using Symfony or Asp.net Core 2.1 (OK)

The execution time of Lavarel is somewhat longer that the one of Symfony 1.4, but not much. 70 ms instead of 50 ms.

When compared to Asp.net core the response time is typically 2-4 times higher, but we are talking about 60 ms as opposed to 15 ms, so the main delay is still in the client rendering and in the network. The main advantage of Laravel, as I see it, is the similarity with other frameworks, including RoR. I havn't compared it against Symfony 2, 3 or 4, but the tendency has been that these frameworks get slower the older they are.

The present purpose is the replace the old version of the rental system with this one.


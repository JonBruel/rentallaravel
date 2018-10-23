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

This has been accomplished. The performance of Lavarel is somewhat better that the one of Symfony, but not much. When compared to Asp.net core the response time is typically 2-3 times higher, but we are talking about 40 ms as opposed to 15 ms, so the main delay is still in the client rendering and in the network. The main advantage of Laravel, as I see it, is the similarity with other frameworks, including RoR. 

The present purpose is the replace the old version of the rental system with this one.


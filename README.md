OCR Element Set (plugin for Omeka)
==================================


This plugin for [Omeka] creates a file element set to manage OCR data.

It can be used in conjunction with [Scripto] to save the transcribed text, or
with [Archive Folder] or [OAI-PMH Static Repository] to import [Mets] and [Alto] 
xml files.


Installation
------------

Uncompress files and rename plugin folder "OcrElementSet".

Then install it like any other Omeka plugin. The plugin has no configuration.

* Note

The default namespace of xsl stylesheets is "http://bibnum.bnf.fr/ns/alto_prod",
used by the [Bibliothèque nationale de France]. Change it if needed.

*Important*

Data can be heavy and they are duplicated by default in the search table of the
base. So it is recommended to install the [fork of Hide Elements] to hide them
from the indexation (just check all boxes for the row "OCR:Data").


Warning
-------

Use it at your own risk.

It's always recommended to backup your files and database so you can roll back
if needed.


Troubleshooting
---------------

See online issues on [OcrElementSet issues] page on GitHub.


License
-------

This plugin is published under the [CeCILL v2.1] licence, compatible with
[GNU/GPL] and approved by [FSF] and [OSI].

In consideration of access to the source code and the rights to copy, modify and
redistribute granted by the license, users are provided only with a limited
warranty and the software's author, the holder of the economic rights, and the
successive licensors only have limited liability.

In this respect, the risks associated with loading, using, modifying and/or
developing or reproducing the software by the user are brought to the user's
attention, given its Free Software status, which may make it complicated to use,
with the result that its use is reserved for developers and experienced
professionals having in-depth computer knowledge. Users are therefore encouraged
to load and test the suitability of the software as regards their requirements
in conditions enabling the security of their systems and/or data to be ensured
and, more generally, to use and operate it in the same conditions of security.
This Agreement may be freely reproduced and published, provided it is not
altered, and that no provisions are either added or removed herefrom.


Contact
-------

Current maintainers:

* Daniel Berthereau (see [Daniel-KM])

First version of this plugin has been built for [Mines ParisTech].


Copyright
---------

* Copyright Daniel Berthereau, 2013-2016


[Omeka]: https://omeka.org
[Scripto]: https://github.com/Omeka/plugin-Scripto
[Archive Folder]: https://github.com/Daniel-KM/ArchiveFolder
[OAI-PMH Static Repository]: https://github.com/Daniel-KM/OaiPmhStaticRepository
[Mets]: https://www.loc.gov/standards/mets
[Alto]: https://www.loc.gov/standards/alto
[fork of Hide Elements]: https://github.com/Daniel-KM/HideElements
[Bibliothèque nationale de France]: http://bnf.fr
[OcrElementSet issues]: https://github.com/Daniel-KM/OcrElementSet/issues
[CeCILL v2.1]: https://www.cecill.info/licences/Licence_CeCILL_V2.1-en.html
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html "GNU/GPL v3"
[FSF]: https://www.fsf.org
[OSI]: http://opensource.org
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
[Mines ParisTech]: http://bib.mines-paristech.fr

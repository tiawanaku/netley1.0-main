# Graph Report - .  (2026-08-06)

## Corpus Check
- 204 files · ~314,957 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 6931 nodes · 20732 edges · 203 communities (189 shown, 14 thin omitted)
- Extraction: 89% EXTRACTED · 11% INFERRED · 0% AMBIGUOUS · INFERRED: 2226 edges (avg confidence: 0.7)
- Token cost: 141,509 input · 0 output

## Community Hubs (Navigation)
- Mixed Filament UI Components
- CodeMirror Editor Internals
- Chart.js Core Engine
- Rich Text Editor (ProseMirror)
- Stats Widget Chart Engine
- Filament Support Utilities
- Markdown Editor (CodeMirror)
- Code Editor Line Rendering
- Rich Editor Marks & Attributes
- Rich Editor ProseMirror Plugins
- Calendar Event Slicing
- Editor & Calendar Cleanup Utils
- Code Editor Block Formatting
- CodeMirror Syntax Tree Nodes
- Filament Form Components Shared Utils
- Chart.js Layout & Ticks
- CodeMirror Document Position Utils
- Table Select Column Badges
- Editor Marks & Date Picker Utils
- Select Field Badges
- Rich Editor Node Views & Commands
- Chart.js Dataset Drawing
- Rich Editor Schema & Commands
- Stat Widget Tick Calculation
- Chart.js Time Scale Utils
- Markdown & Calendar Date Utils
- Filament Profile & Calendar Pages
- File Upload & Code Editor Utils
- Chart.js Bar/Angle Calculations
- Chart.js Option Scopes
- Echo & Upload Test Utilities
- Legal Case Status Enums
- CodeMirror Token Parsing
- Document Category & Case View Pages
- Cliente & Consulta Resource Pages
- Agenda Scheduling Pages
- FullCalendar Rendering Core
- FullCalendar Positioning & Lifecycle
- CodeMirror Cursor & Hover
- CodeMirror Selection Range
- Editor & Chart Color Utils
- FullCalendar Timing & Lifecycle
- Rich Editor Image & Marks
- Filament Support Modal Logic
- FullCalendar Date Marker Utils
- Chart.js Tick Label Layout
- FullCalendar Scroll Sync
- Editor & Calendar Lifecycle Hooks
- Filament Notifications UI
- Stat Widget Draw & Layout
- FullCalendar Fetch & Lifecycle
- Core Domain Models (Eloquent)
- Legal Matters Catalog (Delitos)
- Support & Stat Widget Color Utils
- Chart & Calendar Tick Building
- ProseMirror Transform & Mapping
- Filament Slider Component
- FullCalendar Content Height
- Markdown Block Widgets & Calendar Nodes
- Chart.js Data Sorting
- Filament Cross-Component Utilities
- Markdown Delimiter Parsing
- Stat Widget Data Limits
- CodeMirror Transaction & Mapping
- FullCalendar Component Lifecycle
- CodeMirror Theme & Composition
- Chart.js Controller Updates
- Client Portal & Password Pages
- FullCalendar DOM Caching
- Stat Widget Dataset Drawing
- ProseMirror Position Mapping
- Chart.js Date/Time Parsing
- FullCalendar Cell Building
- Stat Widget Element Updates
- Stat Widget Data Checks
- Filament Support Core Utils
- Chart.js Fill & Interpolation
- Chart.js Legend Generation
- Stat Widget Pie/Bar Geometry
- Cliente & Consulta Seeding
- Filament Support Color/Animation
- Chart.js Responsive Events
- Laravel Echo WebSocket Client
- Personal/Staff Status Enums
- Support & Stat Widget Helpers
- FullCalendar Update State
- Editor & Chart Init Utils
- FullCalendar Options Processing
- Editor & Chart Attribute Utils
- CodeMirror Syntax Highlighting
- ProseMirror DOM Parsing Rules
- Chart.js Dataset Metadata
- Editor Regions & Touch Scroll
- Filament Table Record Selection
- Filament App Shell & Groups
- Stat Widget Device Pixel Ratio
- NPM Dependencies
- Laravel Echo Event Handling
- Stat Widget Animations
- FullCalendar Coordinate Updates
- ProseMirror DOM Position Mapping
- FullCalendar Client Rect Utils
- Filament Support Misc Utils
- Composer Autoload Config
- Consultas Screen UI (Screenshot)
- Filament Color Picker Component
- Auth Login Pages
- Composer Dev Scripts
- CodeMirror DOM Reuse
- Filament Support Apply/Type Utils
- Client Dashboard Page
- Composer Dev Dependencies
- Composer Core Dependencies
- Composer Setup Scripts
- Composer Plugin Config
- Laravel Echo Logging
- Filament Modal Actions
- Laravel Echo Internals
- Editor Text Combine Utils
- FullCalendar Cell Range Slicing
- App Service Provider
- Composer PSR-4 Autoload
- Filament Schema Validation
- FullCalendar Icon Overrides
- Composer Post-Autoload Hooks
- Composer Project Bootstrap
- Filament Support Misc Helpers
- FullCalendar Timestamp Conversion
- Composer Package Keywords
- Base Test Case
- Filament Schema Actions
- Base HTTP Controller
- Client Proceso View
- Personal Agenda View
- Security Epic (Roles & Auditoria)
- Robots.txt Directive

## God Nodes (most connected - your core abstractions)
1. `update()` - 142 edges
2. `t()` - 137 edges
3. `constructor()` - 131 edges
4. `O()` - 129 edges
5. `i()` - 128 edges
6. `r()` - 124 edges
7. `E` - 96 edges
8. `resolve()` - 95 edges
9. `_update()` - 89 edges
10. `_update()` - 87 edges

## Surprising Connections (you probably didn't know these)
- `Materia Legal (Proceso field)` --shares_data_with--> `delitos Table (Legal Matters Catalog, seeder data)`  [INFERRED]
  scrum.txt → database/seeders/data/delitos.txt
- `getExtension()` --indirect_call--> `Ht()`  [INFERRED]
  public/js/filament/forms/components/file-upload.js → public/js/filament/filament/echo.js
- `te()` --indirect_call--> `Pr()`  [INFERRED]
  public/js/filament/forms/components/markdown-editor.js → public/js/filament/filament/echo.js
- `range()` --indirect_call--> `r()`  [INFERRED]
  public/js/filament/forms/components/code-editor.js → public/js/filament/forms/components/textarea.js
- `node()` --indirect_call--> `t()`  [INFERRED]
  public/js/filament/forms/components/code-editor.js → public/js/filament/forms/components/date-time-picker.js

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Netley Product Backlog Epics** — scrum_ep01_personas, scrum_ep02_personal_administrativo, scrum_ep03_clientes, scrum_ep04_procesos, scrum_ep05_finanzas, scrum_ep06_documentos, scrum_ep07_portal_cliente, scrum_ep08_seguridad [EXTRACTED 1.00]
- **Unified Agenda Domain Model (Cita / Llamada / Reunión)** — scrum_agenda, scrum_agenda_cita, scrum_agenda_llamada, scrum_agenda_reunion, scrum_agendaparticipantes, scrum_agendahistorial [EXTRACTED 1.00]
- **Portal Cliente Ejecutivo Architecture Modules** — scrum_portal_cliente, scrum_proceso, scrum_chat_proceso, scrum_expediente_digital, scrum_pago_qr, scrum_notificaciones, scrum_agenda [EXTRACTED 1.00]
- **Consulta Status Lifecycle (Nueva, En Seguimiento, Convertida, Descartada)** — referencias_image_consulta_workflow, referencias_image_nueva_status, referencias_image_en_seguimiento_status, referencias_image_convertida_status, referencias_image_descartada_status [INFERRED 0.85]
- **Gestión Legal App Module Structure (sidebar modules)** — referencias_image_sidebar_navigation, referencias_image_consultas_screen, referencias_image_personal_module, referencias_image_clientes_module, referencias_image_procesos_module, referencias_image_finanzas_module, referencias_image_documentos_module [INFERRED 0.85]

## Communities (203 total, 14 thin omitted)

### Community 0 - "Mixed Filament UI Components"
Cohesion: 0.01
Nodes (275): Cp(), CQ(), g1(), Ph(), et(), _0(), a0(), A2() (+267 more)

### Community 1 - "CodeMirror Editor Internals"
Cohesion: 0.01
Nodes (103): aQ(), as(), b1(), Blockquote(), Br(), Bs(), BulletList(), cf() (+95 more)

### Community 2 - "Chart.js Core Engine"
Cohesion: 0.01
Nodes (128): acquireContext(), active(), addControllers(), addPlugins(), addScales(), ag(), ah(), Al() (+120 more)

### Community 3 - "Rich Text Editor (ProseMirror)"
Cohesion: 0.01
Nodes (127): [g](), addExtensions(), addHackNode(), addNodeMark(), addTextblockHacks(), as(), between(), checkContent() (+119 more)

### Community 4 - "Stats Widget Chart Engine"
Cohesion: 0.02
Nodes (92): addControllers(), addEventListener(), addPlugins(), addScales(), al(), as(), bc(), beforeLayout() (+84 more)

### Community 5 - "Filament Support Utilities"
Cohesion: 0.03
Nodes (47): ys(), Be(), Bu, cq(), cz, dq, Ei(), fi() (+39 more)

### Community 6 - "Markdown Editor (CodeMirror)"
Cohesion: 0.04
Nodes (127): _a(), Ac(), ad(), af(), ai(), An(), ao(), ar() (+119 more)

### Community 7 - "Code Editor Line Rendering"
Cohesion: 0.03
Nodes (133): accept(), add(), addChunk(), addElement(), addEventListener(), addInfoPane(), addInner(), addLineDeco() (+125 more)

### Community 8 - "Rich Editor Marks & Attributes"
Cohesion: 0.06
Nodes (121): ac(), addAttributes(), addKeyboardShortcuts(), Ae(), after(), ap(), at(), au() (+113 more)

### Community 9 - "Rich Editor ProseMirror Plugins"
Cohesion: 0.03
Nodes (119): Kt(), ad(), addInner(), addMark(), addOptions(), addPasteRules(), addProseMirrorPlugins(), addStoredMark() (+111 more)

### Community 10 - "Calendar Event Slicing"
Cohesion: 0.03
Nodes (54): Fa(), _6(), _a(), aM(), ao(), ap(), _b(), B0() (+46 more)

### Community 11 - "Editor & Calendar Cleanup Utils"
Cohesion: 0.04
Nodes (32): themeClasses(), releaseContext(), _4(), c2, ci(), cM(), dd, fd() (+24 more)

### Community 12 - "Code Editor Block Formatting"
Cohesion: 0.03
Nodes (105): _a(), Ac(), addBlock(), ag(), applyChanges(), ay(), balanced(), baseIndent() (+97 more)

### Community 13 - "CodeMirror Syntax Tree Nodes"
Cohesion: 0.04
Nodes (100): Ap(), atLastNode(), between(), bm(), bu(), c1(), child(), childAfter() (+92 more)

### Community 14 - "Filament Form Components Shared Utils"
Cohesion: 0.06
Nodes (94): compare(), eT(), fromJSON(), fS(), jg(), Lb(), map(), $o() (+86 more)

### Community 15 - "Chart.js Layout & Ticks"
Cohesion: 0.04
Nodes (97): aa(), addBox(), addElements(), afterBuildTicks(), afterCalculateLabelRotation(), afterDataLimits(), afterFit(), afterSetDimensions() (+89 more)

### Community 16 - "CodeMirror Document Position Utils"
Cohesion: 0.04
Nodes (45): af(), checkAsyncSchedule(), docViewUpdate(), Ef(), Gu(), localPosFromDOM(), match(), measure() (+37 more)

### Community 17 - "Table Select Column Badges"
Cohesion: 0.07
Nodes (91): A(), addBadgesForSelectedOptions(), addSingleBadge(), addSingleSelectionDisplay(), applyDisabledState(), At(), B(), be() (+83 more)

### Community 18 - "Editor Marks & Date Picker Utils"
Cohesion: 0.05
Nodes (90): u(), allowedMarks(), allowsMarks(), append(), ba(), bm(), $c(), child() (+82 more)

### Community 19 - "Select Field Badges"
Cohesion: 0.07
Nodes (87): La(), addBadgesForSelectedOptions(), addSingleBadge(), addSingleSelectionDisplay(), applyDisabledState(), At(), B(), be() (+79 more)

### Community 20 - "Rich Editor Node Views & Commands"
Cohesion: 0.06
Nodes (87): Xd(), add(), addCommands(), addInputRules(), addNodeView(), Af(), ax(), Bf() (+79 more)

### Community 21 - "Chart.js Dataset Drawing"
Cohesion: 0.05
Nodes (85): adjustHitBoxes(), ae(), afterDraw(), As(), at(), beforeDatasetDraw(), beforeDatasetsDraw(), beforeDraw() (+77 more)

### Community 22 - "Rich Editor Schema & Commands"
Cohesion: 0.04
Nodes (80): vc(), $a(), addNode(), Bc(), constructor(), coordsAtPos(), createCommandManager(), createSchema() (+72 more)

### Community 23 - "Stat Widget Tick Calculation"
Cohesion: 0.05
Nodes (80): addElements(), afterBuildTicks(), afterCalculateLabelRotation(), afterDataLimits(), afterFit(), afterSetDimensions(), afterTickToLabelConversion(), afterUpdate() (+72 more)

### Community 24 - "Chart.js Time Scale Utils"
Cohesion: 0.04
Nodes (77): abutsStart(), after(), before(), bg(), bs(), cc(), count(), Ct() (+69 more)

### Community 25 - "Markdown & Calendar Date Utils"
Cohesion: 0.05
Nodes (37): Id(), _1(), Aa(), ba(), Bi(), Bn(), _d(), de() (+29 more)

### Community 26 - "Filament Profile & Calendar Pages"
Cohesion: 0.06
Nodes (22): EditProfile, AgendaCalendario, EditProfile, ClienteResource, ConsultaResource, HistorialRelationManager, ClienteResource, ConsultaResource (+14 more)

### Community 27 - "File Upload & Code Editor Utils"
Cohesion: 0.05
Nodes (57): Dm(), Lm(), pi(), Wu(), be(), bi(), c(), clickPercent() (+49 more)

### Community 28 - "Chart.js Bar/Angle Calculations"
Cohesion: 0.05
Nodes (73): A(), applyStack(), aspectRatio(), bd(), _calculateBarIndexPixels(), _calculateBarValuePixels(), _computeAngle(), _computeGridLineItems() (+65 more)

### Community 29 - "Chart.js Option Scopes"
Cohesion: 0.04
Nodes (73): Am(), apply(), bc(), chartOptionScopes(), clone(), constructor(), create(), de() (+65 more)

### Community 30 - "Echo & Upload Test Utilities"
Cohesion: 0.15
Nodes (72): Ht(), _getTestState(), at(), Be(), ca(), Cr(), Ct(), D() (+64 more)

### Community 31 - "Legal Case Status Enums"
Cohesion: 0.06
Nodes (14): crearYGenerarContrato(), getCreatedNotification(), getSteps(), handleRecordCreation(), Delito, Finanza, Carbon, Carbon\Carbon (+6 more)

### Community 32 - "CodeMirror Token Parsing"
Cohesion: 0.05
Nodes (67): acceptToken(), addActions(), advance(), advanceFully(), advanceStack(), allActions(), allows(), canShift() (+59 more)

### Community 33 - "Document Category & Case View Pages"
Cohesion: 0.05
Nodes (11): MiProceso, Action, Closure, Documentos, Action, Action, VerCaso, PlanPago (+3 more)

### Community 34 - "Cliente & Consulta Resource Pages"
Cohesion: 0.05
Nodes (23): CreateCliente, ListClientes, CreateConsulta, ListConsultas, ListAgendas, CreateCliente, EditCliente, ListClientes (+15 more)

### Community 35 - "Agenda Scheduling Pages"
Cohesion: 0.06
Nodes (11): MiAgenda, Action, MiAgendaCalendarWidget, AgendaResource, CreateAgenda, EditAgenda, AgendaCalendarWidget, Agenda (+3 more)

### Community 36 - "FullCalendar Rendering Core"
Cohesion: 0.04
Nodes (25): A1(), b4(), bd(), dM, fa(), fq(), gM, gq() (+17 more)

### Community 37 - "FullCalendar Positioning & Lifecycle"
Cohesion: 0.04
Nodes (15): ad, ar, az(), bp(), fs(), H2(), ip(), iu() (+7 more)

### Community 38 - "CodeMirror Cursor & Hover"
Cohesion: 0.05
Nodes (60): active(), bf(), bidiSpans(), bv(), checkHover(), childPos(), coordsAt(), coordsAtPos() (+52 more)

### Community 39 - "CodeMirror Selection Range"
Cohesion: 0.05
Nodes (60): addRange(), Ah(), an(), become(), childCursor(), clear(), compositionView(), Dc() (+52 more)

### Community 40 - "Editor & Chart Color Utils"
Cohesion: 0.05
Nodes (60): zh(), add(), alpha(), Ar(), bh(), Bt(), ca(), _cachedScopes() (+52 more)

### Community 41 - "FullCalendar Timing & Lifecycle"
Cohesion: 0.05
Nodes (12): H0, _i(), ki(), li(), pz, Qe, r2, sq (+4 more)

### Community 42 - "Rich Editor Image & Marks"
Cohesion: 0.07
Nodes (57): Bw(), Image(), shift(), Ah(), ai(), An(), bh(), br() (+49 more)

### Community 43 - "Filament Support Modal Logic"
Cohesion: 0.06
Nodes (45): ar(), bo(), ce(), close(), closeQuietly(), Cr(), ct(), Do() (+37 more)

### Community 44 - "FullCalendar Date Marker Utils"
Cohesion: 0.05
Nodes (16): bs, hd(), hz(), ie(), ih(), ja(), kb, M0 (+8 more)

### Community 45 - "Chart.js Tick Label Layout"
Cohesion: 0.06
Nodes (54): defaultZone(), Ae(), afterAutoSkip(), Ar(), buildLookupTable(), buildTicks(), calculateLabelRotation(), _calculatePadding() (+46 more)

### Community 46 - "FullCalendar Scroll Sync"
Cohesion: 0.05
Nodes (23): bt(), cp(), cu, ds(), eu(), ez(), Jo, kn() (+15 more)

### Community 47 - "Editor & Calendar Lifecycle Hooks"
Cohesion: 0.06
Nodes (17): bn(), au(), bO(), C0(), fM, fz(), gp(), Hb() (+9 more)

### Community 48 - "Filament Notifications UI"
Cohesion: 0.06
Nodes (27): actions(), button(), close(), configureAnimations(), configureTransitions(), constructor(), danger(), dispatch() (+19 more)

### Community 49 - "Stat Widget Draw & Layout"
Cohesion: 0.08
Nodes (51): acquireContext(), adjustHitBoxes(), afterDraw(), At(), bi(), _computeLabelArea(), _computeTitleHeight(), Ct() (+43 more)

### Community 50 - "FullCalendar Fetch & Lifecycle"
Cohesion: 0.04
Nodes (17): a4(), eq(), fetch(), h5(), Hi(), io(), jr(), L0 (+9 more)

### Community 51 - "Core Domain Models (Eloquent)"
Cohesion: 0.07
Nodes (12): AgendaHistorial, Cliente, DocumentoSolicitud, Personal, User, UserFactory, Filament\Models\Contracts\FilamentUser, Filament\Models\Contracts\HasName (+4 more)

### Community 52 - "Legal Matters Catalog (Delitos)"
Cohesion: 0.05
Nodes (49): CIVIL Area, delitos Table (Legal Matters Catalog, seeder data), FAMILIA Area, LABORAL Area, PENAL Area, CIVIL Area, delitos Table (Legal Matters Catalog, root copy), FAMILIA Area (+41 more)

### Community 53 - "Support & Stat Widget Color Utils"
Cohesion: 0.06
Nodes (47): Wa(), _a(), aa(), add(), alpha(), ba(), br(), ca() (+39 more)

### Community 54 - "Chart & Calendar Tick Building"
Cohesion: 0.06
Nodes (47): afterAutoSkip(), Br(), buildLookupTable(), buildTicks(), clear(), Cs(), _d(), da() (+39 more)

### Community 55 - "ProseMirror Transform & Mapping"
Cohesion: 0.07
Nodes (45): accepts(), addMaps(), addStep(), addTransform(), appendMap(), appendMapping(), appendMappingInverted(), apply() (+37 more)

### Community 56 - "Filament Slider Component"
Cohesion: 0.09
Nodes (39): Ae(), ar(), Be(), Bt(), De(), _e(), Ee(), er() (+31 more)

### Community 57 - "FullCalendar Content Height"
Cohesion: 0.05
Nodes (14): C1(), er, f0(), hr, jd, kd(), M4(), pO() (+6 more)

### Community 58 - "Markdown Block Widgets & Calendar Nodes"
Cohesion: 0.07
Nodes (45): activeForPoint(), addBlockWidget(), addChild(), addGaps(), addLeafElement(), addNode(), Ar(), ATXHeading() (+37 more)

### Community 59 - "Chart.js Data Sorting"
Cohesion: 0.06
Nodes (45): ao(), bm(), bo(), Dl(), ef(), first(), fm(), Fo() (+37 more)

### Community 60 - "Filament Cross-Component Utilities"
Cohesion: 0.08
Nodes (36): Dt(), Og(), al(), bl(), cf(), Do(), jo(), kl() (+28 more)

### Community 61 - "Markdown Delimiter Parsing"
Cohesion: 0.06
Nodes (44): addDelimiter(), after(), append(), Ba(), before(), Bg(), boundChange(), commit() (+36 more)

### Community 62 - "Stat Widget Data Limits"
Cohesion: 0.08
Nodes (44): an(), ch(), determineDataLimits(), dh(), diff(), ee(), endOf(), er() (+36 more)

### Community 63 - "CodeMirror Transaction & Mapping"
Cohesion: 0.07
Nodes (43): addChanges(), addMapping(), addSelection(), applyTransaction(), asSingle(), At(), be(), Cn() (+35 more)

### Community 64 - "FullCalendar Component Lifecycle"
Cohesion: 0.07
Nodes (6): dO(), ka, lo(), or, $q(), s3

### Community 65 - "CodeMirror Theme & Composition"
Cohesion: 0.06
Nodes (39): baseTheme(), bQ(), compositionend(), compute(), cS(), dd(), define(), dispatch() (+31 more)

### Community 66 - "Chart.js Controller Updates"
Cohesion: 0.07
Nodes (39): Rg(), afterDatasetsUpdate(), buildOrUpdateControllers(), clear(), De(), _destroy(), _destroyDatasetMeta(), generateLabels() (+31 more)

### Community 67 - "Client Portal & Password Pages"
Cohesion: 0.08
Nodes (14): MisProcesos, CambiarPasswordObligatorio, Action, EnsurePersonalPasswordChanged, AdminPanelProvider, ClientePanelProvider, PersonalPanelProvider, Closure (+6 more)

### Community 68 - "FullCalendar DOM Caching"
Cohesion: 0.11
Nodes (4): cd, id, jp, sM

### Community 69 - "Stat Widget Dataset Drawing"
Cohesion: 0.08
Nodes (36): average(), beforeDatasetDraw(), beforeDatasetsDraw(), beforeDraw(), Bt(), co(), dataset(), getCenterPoint() (+28 more)

### Community 70 - "ProseMirror Position Mapping"
Cohesion: 0.08
Nodes (33): am(), atEnd(), atStart(), cm(), De(), Fr(), fs(), Gn() (+25 more)

### Community 71 - "Chart.js Date/Time Parsing"
Cohesion: 0.08
Nodes (35): daysInMonth(), df(), eu(), Fe(), ff(), Fl(), getAllParsedValues(), getDataTimestamps() (+27 more)

### Community 72 - "FullCalendar Cell Building"
Cohesion: 0.08
Nodes (14): ai(), Bq(), cr, d0(), dr(), f4, h4(), iM (+6 more)

### Community 73 - "Stat Widget Element Updates"
Cohesion: 0.08
Nodes (33): ac(), bh(), buildOrUpdateElements(), cs(), ds(), En(), Es(), getBasePosition() (+25 more)

### Community 74 - "Stat Widget Data Checks"
Cohesion: 0.08
Nodes (33): bl(), cc(), data(), _dataCheck(), dc(), dn(), El(), _exec() (+25 more)

### Community 75 - "Filament Support Core Utils"
Cohesion: 0.21
Nodes (32): _a(), ai(), C(), Cn(), co(), d(), E(), Ee() (+24 more)

### Community 76 - "Chart.js Fill & Interpolation"
Cohesion: 0.09
Nodes (32): ac(), af(), Bi(), ce(), El(), fd(), Fi(), gd() (+24 more)

### Community 77 - "Chart.js Legend Generation"
Cohesion: 0.09
Nodes (31): afterDatasetsUpdate(), beforeLayout(), bl(), cf(), generateLabels(), getDatasetMeta(), getDataVisibility(), getMaxBorderWidth() (+23 more)

### Community 78 - "Stat Widget Pie/Bar Geometry"
Cohesion: 0.12
Nodes (31): applyStack(), _calculateBarIndexPixels(), _calculateBarValuePixels(), calculateCircumference(), _circumference(), _computeAngle(), countVisibleElements(), D() (+23 more)

### Community 79 - "Cliente & Consulta Seeding"
Cohesion: 0.09
Nodes (10): Consulta, ClienteSeeder, ConsultaSeeder, DatabaseSeeder, DelitoSeeder, FinanzaSeeder, PersonalSeeder, ProcesoSeeder (+2 more)

### Community 80 - "Filament Support Color/Animation"
Cohesion: 0.20
Nodes (30): aa(), ba(), br(), Bt(), Ca(), Da(), Dn(), Fi() (+22 more)

### Community 81 - "Chart.js Responsive Events"
Cohesion: 0.09
Nodes (30): ad(), addEventListener(), bindResponsiveEvents(), cd(), contains(), data(), ed(), fa() (+22 more)

### Community 82 - "Laravel Echo WebSocket Client"
Cohesion: 0.09
Nodes (15): ar(), b(), cr(), g(), ir(), Me(), nr(), P() (+7 more)

### Community 83 - "Personal/Staff Status Enums"
Cohesion: 0.08
Nodes (6): ClienteFactory, ConsultaFactory, FinanzaFactory, PersonalFactory, ProcesoFactory, Illuminate\Database\Eloquent\Factories\Factory

### Community 84 - "Support & Stat Widget Helpers"
Cohesion: 0.16
Nodes (28): ae(), B(), de(), dt(), Ge(), Gt(), h(), Ie() (+20 more)

### Community 85 - "FullCalendar Update State"
Cohesion: 0.10
Nodes (13): Ae(), ct(), du(), lM(), mo(), oe, oM(), pa() (+5 more)

### Community 86 - "Editor & Chart Init Utils"
Cohesion: 0.09
Nodes (26): apply(), computeBlockGapDeco(), defineModifier(), init(), isDone(), k1(), kg(), lg() (+18 more)

### Community 87 - "FullCalendar Options Processing"
Cohesion: 0.10
Nodes (12): a3, d3, f3(), h3(), j1, kp(), n2, q3() (+4 more)

### Community 88 - "Editor & Chart Attribute Utils"
Cohesion: 0.11
Nodes (25): Ab(), am(), attrs(), Bl(), BP(), $c(), ci(), Eb() (+17 more)

### Community 89 - "CodeMirror Syntax Highlighting"
Cohesion: 0.09
Nodes (25): addActive(), chunkEnd(), findIndex(), getCursor(), gotoInner(), highlight(), ic(), length() (+17 more)

### Community 90 - "ProseMirror DOM Parsing Rules"
Cohesion: 0.14
Nodes (25): addAll(), addDOM(), addElement(), addElementByRule(), addTextNode(), addToSet(), allowsMarkType(), closeExtra() (+17 more)

### Community 91 - "Chart.js Dataset Metadata"
Cohesion: 0.12
Nodes (24): Pa(), au(), average(), dataset(), getCenterPoint(), getProps(), getRange(), getSortedVisibleDatasetMetas() (+16 more)

### Community 92 - "Editor Regions & Touch Scroll"
Cohesion: 0.13
Nodes (9): findRegions(), bM(), eO(), hp(), Mz(), _n(), nd(), pd() (+1 more)

### Community 93 - "Filament Table Record Selection"
Cohesion: 0.21
Nodes (20): areRecordsSelected(), areRecordsToggleable(), canSelectAllRecords(), deselectAllRecords(), deselectRecords(), f(), getRecordsOnPage(), getSelectedRecordsCount() (+12 more)

### Community 94 - "Filament App Shell & Groups"
Cohesion: 0.13
Nodes (12): B(), C(), close(), init(), J(), S(), setUpResizeObserver(), T() (+4 more)

### Community 95 - "Stat Widget Device Pixel Ratio"
Cohesion: 0.11
Nodes (22): apply(), chartOptionScopes(), _computeLabelSizes(), constructor(), describe(), ga(), getDevicePixelRatio(), getMeta() (+14 more)

### Community 96 - "NPM Dependencies"
Cohesion: 0.10
Nodes (19): axios, concurrently, laravel-vite-plugin, devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss (+11 more)

### Community 97 - "Laravel Echo Event Handling"
Cohesion: 0.15
Nodes (19): Ce(), De(), ei(), Fe(), He(), Ie(), ii(), le() (+11 more)

### Community 98 - "Stat Widget Animations"
Cohesion: 0.14
Nodes (18): active(), _animateOptions(), cancel(), _createAnimations(), _createDescriptors(), _descriptors(), invalidate(), _notify() (+10 more)

### Community 99 - "FullCalendar Coordinate Updates"
Cohesion: 0.14
Nodes (5): _2(), Aq, ga, La, uq()

### Community 100 - "ProseMirror DOM Position Mapping"
Cohesion: 0.15
Nodes (17): Dc(), domAfterPos(), domFromPos(), Ec(), Fi(), focus(), Hi(), im() (+9 more)

### Community 101 - "FullCalendar Client Rect Utils"
Cohesion: 0.13
Nodes (4): dt, gs(), hs(), Zb()

### Community 102 - "Filament Support Misc Utils"
Cohesion: 0.22
Nodes (15): En(), fa(), mr(), On(), Pn(), pt(), Qr(), ra() (+7 more)

### Community 103 - "Composer Autoload Config"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 104 - "Consultas Screen UI (Screenshot)"
Cohesion: 0.14
Nodes (14): Clientes Module, Consulta Filters Panel (búsqueda, ciudad, tipo, origen), Consulta Status Lifecycle (Nueva -> En Seguimiento -> Convertida/Descartada), Consultas Management Screen (Gestión Legal), Estado: Convertida, Estado: Descartada, Documentos Module, Estado: En Seguimiento (+6 more)

### Community 106 - "Auth Login Pages"
Cohesion: 0.21
Nodes (4): Login, Login, Filament\Auth\Pages\Login, Filament\Schemas\Components\Component

### Community 107 - "Composer Dev Scripts"
Cohesion: 0.18
Nodes (11): scripts, dev, post-update-cmd, pre-package-uninstall, test, Composer\\Config::disableProcessTimeout, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall, npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1 --timeout=0\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,logs,vite --kill-others (+3 more)

### Community 108 - "CodeMirror DOM Reuse"
Cohesion: 0.29
Nodes (10): canReuseDOM(), createDOM(), hf(), nf(), Pc(), reuseDOM(), setAttrs(), setDOM() (+2 more)

### Community 109 - "Filament Support Apply/Type Utils"
Cohesion: 0.27
Nodes (10): apply(), as(), At(), it(), Ka(), Mt(), _o(), rr() (+2 more)

### Community 111 - "Composer Dev Dependencies"
Cohesion: 0.22
Nodes (9): require-dev, fakerphp/faker, laravel/pail, laravel/pint, laravel/sail, mockery/mockery, nunomaduro/collision, pestphp/pest (+1 more)

### Community 112 - "Composer Core Dependencies"
Cohesion: 0.25
Nodes (8): require, barryvdh/laravel-dompdf, endroid/qr-code, filament/filament, laravel/framework, laravel/tinker, php, saade/filament-fullcalendar

### Community 113 - "Composer Setup Scripts"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 114 - "Composer Plugin Config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 115 - "Laravel Echo Logging"
Cohesion: 0.29
Nodes (6): Be(), di(), e(), i(), Ut(), xr()

### Community 116 - "Filament Modal Actions"
Cohesion: 0.73
Nodes (5): closeModal(), generateModalId(), init(), openModal(), syncActionModals()

### Community 117 - "Laravel Echo Internals"
Cohesion: 0.33
Nodes (6): a(), at(), H(), ji(), L(), pt()

### Community 118 - "Editor Text Combine Utils"
Cohesion: 0.33
Nodes (6): combine(), oQ(), Te(), WX(), ed(), un()

### Community 121 - "Composer PSR-4 Autoload"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 124 - "Composer Post-Autoload Hooks"
Cohesion: 0.50
Nodes (4): post-autoload-dump, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, @php artisan filament:upgrade, @php artisan package:discover --ansi

### Community 125 - "Composer Project Bootstrap"
Cohesion: 0.50
Nodes (4): post-create-project-cmd, @php artisan key:generate --ansi, @php artisan migrate --graceful --ansi, @php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\

### Community 126 - "Filament Support Misc Helpers"
Cohesion: 0.50
Nodes (4): Rt(), Ut(), Z(), ze()

### Community 129 - "Composer Package Keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

## Knowledge Gaps
- **126 isolated node(s):** `Controller`, `$schema`, `name`, `type`, `description` (+121 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **14 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Ih()` connect `CodeMirror Editor Internals` to `Mixed Filament UI Components`, `Chart.js Core Engine`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Why does `Ne` connect `Editor & Calendar Cleanup Utils` to `Mixed Filament UI Components`, `Stat Widget Animations`, `Stats Widget Chart Engine`, `Stat Widget Element Updates`, `Stat Widget Data Checks`, `FullCalendar Date Marker Utils`, `Chart.js Tick Label Layout`, `FullCalendar Scroll Sync`, `Stat Widget Draw & Layout`, `Support & Stat Widget Helpers`, `Stat Widget Tick Calculation`, `Filament Cross-Component Utilities`, `Stat Widget Data Limits`, `Stat Widget Device Pixel Ratio`?**
  _High betweenness centrality (0.048) - this node is a cross-community bridge._
- **Why does `p()` connect `CodeMirror Document Position Utils` to `Mixed Filament UI Components`, `Chart.js Core Engine`, `Filament Support Utilities`, `Rich Editor Marks & Attributes`, `Rich Editor ProseMirror Plugins`, `Calendar Event Slicing`, `Editor & Calendar Cleanup Utils`, `CodeMirror Syntax Tree Nodes`, `Filament Form Components Shared Utils`, `Chart.js Layout & Ticks`, `Editor Marks & Date Picker Utils`, `Rich Editor Node Views & Commands`, `Chart.js Dataset Drawing`, `File Upload & Code Editor Utils`, `Chart.js Bar/Angle Calculations`, `Echo & Upload Test Utilities`, `Filament Support Modal Logic`, `Chart.js Tick Label Layout`, `FullCalendar Scroll Sync`, `Stat Widget Draw & Layout`, `FullCalendar Fetch & Lifecycle`, `Support & Stat Widget Color Utils`, `Chart & Calendar Tick Building`, `Markdown Block Widgets & Calendar Nodes`, `Chart.js Data Sorting`, `Filament Cross-Component Utilities`, `Stat Widget Data Limits`, `FullCalendar Cell Building`, `Stat Widget Element Updates`, `Filament Support Core Utils`, `Chart.js Fill & Interpolation`, `Stat Widget Pie/Bar Geometry`, `Laravel Echo WebSocket Client`, `Editor Regions & Touch Scroll`, `Filament App Shell & Groups`, `Laravel Echo Logging`?**
  _High betweenness centrality (0.039) - this node is a cross-community bridge._
- **Are the 23 inferred relationships involving `update()` (e.g. with `Ls()` and `a()`) actually correct?**
  _`update()` has 23 INFERRED edges - model-reasoned connections that need verification._
- **Are the 136 inferred relationships involving `t()` (e.g. with `add()` and `append()`) actually correct?**
  _`t()` has 136 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `constructor()` (e.g. with `a()` and `h()`) actually correct?**
  _`constructor()` has 13 INFERRED edges - model-reasoned connections that need verification._
- **Are the 126 inferred relationships involving `i()` (e.g. with `add()` and `addElement()`) actually correct?**
  _`i()` has 126 INFERRED edges - model-reasoned connections that need verification._
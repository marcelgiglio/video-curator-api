<?php
// Inclusão da classe Database para operações de banco de dados.
require_once 'database.php';

/**
 * A classe FilterRegistry é responsável por registrar e fornecer instâncias de filtros.
 * Ela mantém um mapa de filtros que associa uma chave de filtro a uma classe de filtro correspondente.
 */
class FilterRegistry {
    private static $filters = [];

    /**
     * Registra uma classe de filtro no mapa de filtros usando uma chave específica.
     * @param string $key Chave para identificar o filtro.
     * @param string $className Nome da classe do filtro.
     */
    public static function register($key, $className) {
        self::$filters[$key] = $className;
    }

    /**
     * Obtém uma instância de filtro baseada na chave fornecida.
     * Se nenhum filtro específico for encontrado, retorna uma instância do DefaultFilter.
     * @param string $key Chave do filtro a ser obtido.
     * @return Filter Instância do filtro.
     */
    public static function getFilter($key) {
        $filterClass = self::$filters[$key] ?? DefaultFilter::class;
        return new $filterClass();
    }
}

/**
 * Interface para filtros que definem o método apply para aplicar condições SQL através de um QueryBuilder.
 */
interface Filter {
    public function apply($queryBuilder, $value);
}

/**
 * OrderByFilter organiza os resultados da consulta com base no campo especificado e na direção (ascendente ou descendente).
 */
class OrderByFilter implements Filter {
    public function apply($queryBuilder, $value) {
        $direction = str_starts_with($value, '-') ? 'DESC' : 'ASC';
        $field = ltrim($value, '-');
        $queryBuilder->orderBy($field, $direction);
    }
}
OrderByFilter::register('sort', OrderByFilter::class);  // Auto-registro do filtro de ordenação.

/**
 * LanguageExclusionFilter exclui resultados baseados nos idiomas especificados.
 */
class LanguageExclusionFilter implements Filter {
    public function apply($queryBuilder, $value) {
        $queryBuilder->whereNotIn('original_language', (array) $value);
    }
}
LanguageExclusionFilter::register('exclude_language', LanguageExclusionFilter::class);  // Auto-registro do filtro de exclusão de idioma.

/**
 * DefaultFilter aplica condições de igualdade simples baseadas no campo e valor especificados.
 */
class DefaultFilter implements Filter {
    public function apply($queryBuilder, $value) {
        $queryBuilder->where($key, $value);
    }
}
DefaultFilter::register('default', DefaultFilter::class);  // Auto-registro do filtro padrão.

/**
 * QueryBuilder constrói consultas SQL dinâmicas com base nos filtros aplicados.
 */
class QueryBuilder {
    protected $conditions = [];
    protected $orderBy = [];

    public function where($field, $value, $operator = '=') {
        $this->conditions[] = "$field $operator '$value'";
    }

    public function whereNotIn($field, array $values) {
        $escapedValues = array_map(fn($val) => "'$val'", $values);
        $this->conditions[] = "$field NOT IN (" . implode(', ', $escapedValues) . ")";
    }

    public function orderBy($field, $direction) {
        $this->orderBy[] = "$field $direction";
    }

    public function toSql() {
        $sql = "SELECT * FROM videos";
        if (!empty($this->conditions)) {
            $sql .= " WHERE " . implode(' AND ', $this->conditions);
        }
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }
        return $sql;
    }
}

/**
 * VideoSearch coordena a aplicação dos filtros e execução da consulta construída pelo QueryBuilder.
 */
class VideoSearch {
    private $db;
    private $queryBuilder;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->queryBuilder = new QueryBuilder();
    }

    /**
     * Aplica os filtros especificados pelos parâmetros aos critérios de busca.
     * @param array $params Parâmetros de filtro provenientes da requisição HTTP.
     */
    public function applyFilters($params) {
        foreach ($params as $key => $value) {
            $filter = FilterRegistry::getFilter($key);
            $filter->apply($this->queryBuilder, $value);
        }
    }

    /**
     * Executa a consulta SQL construída e retorna os resultados.
     * @return array Resultados da consulta.
     */
    public function execute() {
        $sql = $this->queryBuilder->toSql();
        return $this->db->executeQuery($sql, [], PDO::FETCH_ASSOC);
    }
}

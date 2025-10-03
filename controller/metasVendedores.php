<?php
class ControllerGerencialMetasVendedores extends Controller {

    public function index() {
        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['vendedores'] = $this->getVendedores();
        $data['metas'] = $this->getDatas();

        $this->response->setOutput($this->load->view('gerencial/metasVendedores', $data));
    }

    public function cadastrarMeta() {
        $this->load->model('gerencial/metasVendedores');

        $user_id = $this->request->post['user_id'] ?? '';
        $valor_da_meta = $this->request->post['valor_da_meta'] ?? '';
        $data_fechamento = $this->request->post['data_fechamento'] ?? '';

        if (!empty($valor_da_meta)) {
            $valor_da_meta = str_replace('.', '', $valor_da_meta);
            $valor_da_meta = substr($valor_da_meta, 0, -2) . '.' . substr($valor_da_meta, -2);
        }

        // Data para o banco
        if (!empty($data_fechamento)) {
            $data_fechamento = str_replace('/', '-', $data_fechamento);
            $data_fechamento = date('Y-m-d', strtotime($data_fechamento));
        }

        $meta = [
            'user_id' => $user_id,
            'valor_da_meta' => $valor_da_meta,
            'data_fechamento' => $data_fechamento
        ];

        $res = $this->model_gerencial_metasVendedores->cadastrarMeta($meta);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'success' => $res ? true : false,
            'data' => $meta
        ]));
    }

    public function atualizarMeta() {
        $this->load->model('gerencial/metasVendedores');

        $user_id = $this->request->post['user_id'] ?? '';
        $valor_da_meta = $this->request->post['valor_da_meta'] ?? '';
        $data_fechamento = $this->request->post['data_fechamento'] ?? '';

        if (!empty($valor_da_meta)) {
            $valor_da_meta = str_replace('.', '', $valor_da_meta);
            $valor_da_meta = substr($valor_da_meta, 0, -2) . '.' . substr($valor_da_meta, -2);
        }

        // Data para o banco
        if (!empty($data_fechamento)) {
            $data_fechamento = str_replace('/', '-', $data_fechamento);
            $data_fechamento = date('Y-m-d', strtotime($data_fechamento));
        }

        $meta = [
            'user_id' => $user_id,
            'valor_da_meta' => $valor_da_meta,
            'data_fechamento' => $data_fechamento
        ];

        $res = $this->model_gerencial_metasVendedores->atualizarMeta($meta);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'success' => $res ? true : false,
            'data' => $meta
        ]));
    }

    public function getVendedores() {
        $this->load->model('gerencial/metasVendedores');
        $vendedores = $this->model_gerencial_metasVendedores->getVendedores();

        foreach ($vendedores as $key => $vendedor) {
            if (isset($vendedor['firstname'], $vendedor['lastname'])) {
                $vendedores[$key]['nomeCompleto'] = trim($vendedor['firstname']) . ' ' . trim($vendedor['lastname']);
            }
        }

        return $vendedores;
    }

    public function getDatas() {
        $this->load->model('gerencial/metasVendedores');
        $dados = $this->model_gerencial_metasVendedores->getDatas();

        foreach ($dados as $key => $item) {
            // Nome completo
            if (isset($item['firstname'], $item['lastname'])) {
                $dados[$key]['vendedor'] = trim($item['firstname']) . ' ' . trim($item['lastname']);
            }

            // Data de fechamento em formato BR
            if (isset($item['data_fechamento']) && $item['data_fechamento'] != '0000-00-00') {
                $dados[$key]['data_fechamento'] = date('d-m-Y', strtotime($item['data_fechamento']));
            }

            // Valor da meta em formato BR
            if (isset($item['valor_da_meta'])) {
                $dados[$key]['valor_da_meta'] = number_format((float)$item['valor_da_meta'], 2, ',', '.');
            }
        }

        return $dados;
    }

    // Recarregar tabela via AJAX
    public function carregarTabela() {
        $dados = $this->getDatas();
        $html = '';

        if ($dados) {
            foreach ($dados as $meta) {
                $html .= '<tr data-id="' . $meta['user_id'] . '" data-meta="' . $meta['valor_da_meta'] . '" data-data="' . $meta['data_fechamento'] . '">';
                $html .= '<td>' . $meta['vendedor'] . '</td>';
                $html .= '<td>' . $meta['valor_da_meta'] . '</td>';
                $html .= '<td>' . $meta['data_fechamento'] . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="4" class="text-center">Sem resultados.</td></tr>';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['html' => $html]));
    }
}
?>
